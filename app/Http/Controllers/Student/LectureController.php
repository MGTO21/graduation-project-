<?php

namespace App\Http\Controllers\Student;

use App\Models\CourseOffering;
use App\Models\Lecture;
use App\Models\LectureSchedule;
use App\Http\Controllers\Controller;

class LectureController extends Controller
{
    /**
     * معرفات مواعيد جدول الطالب - نحتاجها عشان نلقط محاضرات مقرراته بس (قسمه وسمستره،
     * زائد المقررات المشتركة بين كل الأقسام). نفس منطق DashboardController بالظبط.
     */
    private function scheduleIdsFor($student)
    {
        $courseOfferingIds = CourseOffering::where(function ($query) use ($student) {
                $query->where('department_id', $student->department_id)
                      ->orWhereNull('department_id');
            })
            ->where('semester_id', $student->semester_id)
            ->where('is_active', true)
            ->pluck('id');

        return LectureSchedule::whereIn('course_offering_id', $courseOfferingIds)->pluck('id');
    }

    /**
     * المحاضرات المرفوعة (كل محاضرات مقررات الطالب مع ملفاتها إن وجدت)
     */
    public function index()
    {
        $student = auth()->user();

        $lectures = Lecture::with([
            'lectureSchedule.courseOffering.course',
            'lectureSchedule.courseOffering.lecturer',
            'files',
        ])
        ->whereIn('lecture_schedule_id', $this->scheduleIdsFor($student))
        ->orderByDesc('lecture_date')
        ->get();

        return view('student.lectures.uploaded', compact('lectures'));
    }

    /**
     * محاضرات سابقة: بثوث مباشرة انتهت (status = ended) لمقررات الطالب
     */
    public function past()
    {
        $student = auth()->user();

        $pastLectures = Lecture::with([
            'lectureSchedule.courseOffering.course',
            'lectureSchedule.courseOffering.lecturer',
            'files',
        ])
        ->whereIn('lecture_schedule_id', $this->scheduleIdsFor($student))
        ->where('status', 'ended')
        ->orderByDesc('lecture_date')
        ->get();

        return view('student.lectures.past', compact('pastLectures'));
    }

    /**
     * عرض محاضرة واحدة مع ملفاتها (مشاهدة الفيديو والصوت وتحميل pdf)
     */
    public function show(Lecture $lecture)
    {
        $student = auth()->user();

        $courseOffering = $lecture->lectureSchedule->courseOffering;

        // حماية: الطالب لا يصل إلا لمحاضرات قسمه وسمستره - إلا لو المقرر "كل الأقسام"
        // (department_id فاضي = مادة مشتركة، يشوفها طلاب كل الأقسام في نفس السمستر)
        $sameDepartment = $courseOffering->department_id === null
            || $courseOffering->department_id === $student->department_id;

        if (! $sameDepartment || $courseOffering->semester_id !== $student->semester_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه المحاضرة.');
        }

        $lecture->load([
            'lectureSchedule.courseOffering.course',
            'lectureSchedule.courseOffering.lecturer',
            'files',
        ]);

        return view('student.lectures.show', compact('lecture'));
    }
}
