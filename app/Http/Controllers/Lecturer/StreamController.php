<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\CourseOffering;
use App\Models\Lecture;
use App\Models\LectureSchedule;
use Illuminate\Support\Str;

class StreamController extends Controller
{
    /**
     * بدء بث المحاضرة: نعمل (أو نستخدم) سجل Lecture الخاص بموعد اليوم، ونحوّل حالته لـ live
     *
     * ملاحظة: نستخدم firstOrNew مش create مباشرة، عشان لو المحاضر ضغط "بدء المحاضرة" أكتر من مرة
     * لنفس اليوم (مثلاً رجع بعد ما قفل المتصفح غلط) ما ننشئ سجل مكرر، نكمل على نفس السجل.
     */
    public function start(LectureSchedule $lectureSchedule)
    {
        // حماية: المحاضر لا يبدأ بث إلا لمقرراته هو
        if ($lectureSchedule->courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك ببدء بث هذا المقرر.');
        }

        $lecture = Lecture::firstOrNew([
            'lecture_schedule_id' => $lectureSchedule->id,
            'lecture_date'        => now()->toDateString(),
        ]);

        $lecture->status = 'live';
        $lecture->started_at = now();

        // معرف عشوائي فريد نستخدمه كـ Peer ID في PeerJS - الطلاب بيتصلوا بيه عشان يستقبلوا الفيديو
        // لو أصلاً عندنا meeting_id من قبل (مثلاً بث انقطع وبيرجع) نسيبه زي ما هو
        if (! $lecture->meeting_id) {
            $lecture->meeting_id = (string) Str::uuid();
        }

        $lecture->save();

        return redirect()->route('lecturer.stream.show', $lecture);
    }

    /**
     * بث طارئ: للحالات اللي المحاضر يحتاج يفتح بث فوري لمقرر بيدرسه في يوم ما فيه موعد
     * مجدول أصلاً (زي محاضرة تعويضية أو ظرف طارئ). ما عملنا تعديل على قاعدة البيانات لهذا -
     * بنستخدم أي موعد موجود أصلاً لنفس المقرر كـ "حامل" لسجل Lecture (لازم لسه نربط
     * lecture_schedule_id لأنه عمود إلزامي في الجدول)، لكن lecture_date بيتسجل بتاريخ اليوم
     * الحقيقي بغض النظر عن يوم الموعد المستخدم. يوم الموعد نفسه غير مهم هنا لأن كشف
     * "فيه بث مباشر الآن" (عند الطالب والمحاضر) يعتمد على status=live مش على مطابقة اليوم.
     */
    public function startEmergency(CourseOffering $courseOffering)
    {
        // حماية: المحاضر لا يبدأ بث إلا لمقرراته هو
        if ($courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك ببدء بث هذا المقرر.');
        }

        $scheduleIds = $courseOffering->lectureSchedules()->pluck('id');

        // لو أصلاً في بث شغال (عادي أو طارئ) لنفس المقرر، نكمل عليه بدل إنشاء سجل جديد
        $existingLive = Lecture::whereIn('lecture_schedule_id', $scheduleIds)
            ->where('status', 'live')
            ->first();

        if ($existingLive) {
            return redirect()->route('lecturer.stream.show', $existingLive);
        }

        $carrierSchedule = $courseOffering->lectureSchedules()->first();

        if (! $carrierSchedule) {
            return back()->with('error', 'يجب أن يكون لهذا المقرر موعد واحد على الأقل في الجدول الأسبوعي (من لوحة الأدمن) قبل إمكانية بدء بث طارئ.');
        }

        $lecture = Lecture::firstOrNew([
            'lecture_schedule_id' => $carrierSchedule->id,
            'lecture_date'        => now()->toDateString(),
        ]);

        $lecture->status = 'live';
        $lecture->started_at = now();

        if (! $lecture->meeting_id) {
            $lecture->meeting_id = (string) Str::uuid();
        }

        $lecture->save();

        return redirect()->route('lecturer.stream.show', $lecture);
    }

    /**
     * صفحة بث المحاضرة للمحاضر (نفس منطق PeerJS القديم لكن مربوط بـ meeting_id الحقيقي)
     */
    public function show(Lecture $lecture)
    {
        $courseOffering = $lecture->lectureSchedule->courseOffering;

        if ($courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى بث هذه المحاضرة.');
        }

        $lecture->load('lectureSchedule.courseOffering.course');

        // نجيب رسائل شات المقرر عشان تبان جنب الفيديو من أول ما الصفحة تفتح (نفس منطق ChatController)
        $messages = ChatMessage::with('user')
            ->where('course_offering_id', $courseOffering->id)
            ->orderByDesc('id')
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return view('lecturer.stream', compact('lecture', 'courseOffering', 'messages'));
    }

    /**
     * إنهاء البث: نسجل وقت النهاية ونرجع حالة المحاضرة لـ ended
     */
    public function end(Lecture $lecture)
    {
        $courseOffering = $lecture->lectureSchedule->courseOffering;

        if ($courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بإنهاء بث هذه المحاضرة.');
        }

        $lecture->status = 'ended';
        $lecture->ended_at = now();
        $lecture->save();

        return redirect()
            ->route('lecturer.dashboard')
            ->with('success', 'تم إنهاء البث بنجاح.');
    }
}
