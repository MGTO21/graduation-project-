<?php

namespace App\Http\Controllers\Student;

use App\Models\Lecture;
use App\Http\Controllers\Controller;

class LectureController extends Controller
{
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
