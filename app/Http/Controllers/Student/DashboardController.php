<?php

namespace App\Http\Controllers\Student;

use App\Models\CourseOffering;
use App\Models\Lecture;
use App\Models\LectureSchedule;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * أيام الأسبوع الدراسية مع ترجمتها العربية
     */
    private array $days = [
        'Saturday'  => 'السبت',
        'Sunday'    => 'الأحد',
        'Monday'    => 'الاثنين',
        'Tuesday'   => 'الثلاثاء',
        'Wednesday' => 'الأربعاء',
        'Thursday'  => 'الخميس',
    ];

    /**
     * لوحة الطالب: جدوله الأسبوعي ومقررات سمستره ومحاضراتها
     */
    public function index()
    {
        $student = auth()->user()->load(['department', 'semester']);

        // مقررات قسم وسمستر الطالب - مع المقررات المشتركة بين كل الأقسام (department_id فاضي)
        $courseOfferings = CourseOffering::with([
            'course',
            'lecturer',
        ])
        ->where(function ($query) use ($student) {
            $query->where('department_id', $student->department_id)
                  ->orWhereNull('department_id');
        })
        ->where('semester_id', $student->semester_id)
        ->where('is_active', true)
        ->get();

        // الجدول الأسبوعي لمقررات الطالب مجمعاً حسب اليوم
        $schedules = LectureSchedule::with([
            'courseOffering.course',
            'courseOffering.lecturer',
        ])
        ->whereIn('course_offering_id', $courseOfferings->pluck('id'))
        ->orderBy('start_time')
        ->get()
        ->groupBy('day');

        // المحاضرات المرفوعة لمقررات الطالب
        $scheduleIds = LectureSchedule::whereIn('course_offering_id', $courseOfferings->pluck('id'))->pluck('id');

        $lectures = Lecture::with([
            'lectureSchedule.courseOffering.course',
            'lectureSchedule.courseOffering.lecturer',
            'files',
        ])
        ->whereIn('lecture_schedule_id', $scheduleIds)
        ->orderByDesc('lecture_date')
        ->get();

        // محاضرة مباشرة الآن (إن وجدت) لأحد مقررات الطالب - تظهر كبطاقة بارزة فوق اللوحة
        $liveLecture = Lecture::with(['lectureSchedule.courseOffering.course'])
            ->whereIn('lecture_schedule_id', $scheduleIds)
            ->where('status', 'live')
            ->first();

        // محاضرات سابقة (بثوث انتهت) لعرضها في قسم منفصل مع ملفاتها إن رُفعت
        $pastLectures = Lecture::with([
            'lectureSchedule.courseOffering.course',
            'lectureSchedule.courseOffering.lecturer',
            'files',
        ])
        ->whereIn('lecture_schedule_id', $scheduleIds)
        ->where('status', 'ended')
        ->orderByDesc('lecture_date')
        ->get();

        $days = $this->days;

        return view('student.dashboard', compact(
            'student', 'courseOfferings', 'schedules', 'lectures', 'days', 'liveLecture', 'pastLectures'
        ));
    }
}
