<?php

namespace App\Http\Controllers\Student;

use App\Models\CourseOffering;
use App\Models\Lecture;
use App\Models\LectureSchedule;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

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
     * مقررات قسم وسمستر الطالب - مع المقررات المشتركة بين كل الأقسام (department_id فاضي).
     * منطق مكرر بين أكتر من صفحة (الرئيسية، الجدول، المحاضرات)، فسحبناه لدالة واحدة هنا
     */
    private function courseOfferingsFor(User $student): Collection
    {
        return CourseOffering::with(['course', 'lecturer'])
            ->where(function ($query) use ($student) {
                $query->where('department_id', $student->department_id)
                      ->orWhereNull('department_id');
            })
            ->where('semester_id', $student->semester_id)
            ->where('is_active', true)
            ->get();
    }

    /**
     * لوحة الطالب الرئيسية: مقررات سمستره + بطاقة البث المباشر إن وجد
     */
    public function index()
    {
        $student = auth()->user()->load(['department', 'semester']);

        $courseOfferings = $this->courseOfferingsFor($student);

        $scheduleIds = LectureSchedule::whereIn('course_offering_id', $courseOfferings->pluck('id'))->pluck('id');

        // محاضرة مباشرة الآن (إن وجدت) لأحد مقررات الطالب - تظهر كبطاقة بارزة فوق اللوحة
        $liveLecture = Lecture::with(['lectureSchedule.courseOffering.course'])
            ->whereIn('lecture_schedule_id', $scheduleIds)
            ->where('status', 'live')
            ->first();

        return view('student.dashboard', compact('student', 'courseOfferings', 'liveLecture'));
    }

    /**
     * صفحة الجدول الأسبوعي لوحدها (كانت جوا الرئيسية، فصلناها لتبويب مستقل في القائمة الجانبية)
     */
    public function schedule()
    {
        $student = auth()->user();

        $courseOfferings = $this->courseOfferingsFor($student);

        $schedules = LectureSchedule::with([
            'courseOffering.course',
            'courseOffering.lecturer',
        ])
        ->whereIn('course_offering_id', $courseOfferings->pluck('id'))
        ->orderBy('start_time')
        ->get()
        ->groupBy('day');

        $days = $this->days;

        return view('student.schedule', compact('schedules', 'days'));
    }
}
