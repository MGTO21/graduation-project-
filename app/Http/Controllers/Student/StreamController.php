<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Lecture;

class StreamController extends Controller
{
    /**
     * صفحة مشاهدة البث المباشر للطالب (نفس منطق PeerJS القديم لكن receive-only ومربوط بـ meeting_id حقيقي)
     */
    public function show(Lecture $lecture)
    {
        $student = auth()->user();
        $courseOffering = $lecture->lectureSchedule->courseOffering;

        // حماية: الطالب لا يصل إلا لبث مقررات قسمه وسمستره - إلا لو المقرر "كل الأقسام"
        $sameDepartment = $courseOffering->department_id === null
            || $courseOffering->department_id === $student->department_id;

        if (! $sameDepartment || $courseOffering->semester_id !== $student->semester_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا البث.');
        }

        // لو المحاضر أنهى البث أو ما بدأه بعد، ما فيه فايدة نفتح صفحة بث فاضية
        if ($lecture->status !== 'live') {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'هذه المحاضرة غير مباشرة حالياً.');
        }

        $lecture->load(['lectureSchedule.courseOffering.course', 'lectureSchedule.courseOffering.lecturer']);

        // نجيب رسائل شات المقرر عشان تبان جنب الفيديو من أول ما الصفحة تفتح (نفس منطق ChatController)
        $messages = ChatMessage::with('user')
            ->where('course_offering_id', $courseOffering->id)
            ->orderByDesc('id')
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return view('student.stream', compact('lecture', 'courseOffering', 'messages'));
    }
}
