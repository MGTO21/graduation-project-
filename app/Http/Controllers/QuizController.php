<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\Quiz;
use Illuminate\Http\Request;

/*
 * نفس فلسفة الشات بالظبط: ما فيه WebSocket ولا Broadcasting هنا. المحاضر (في صفحة
 * المتابعة الحية) والطالب (في صفحة الإجابة وبعدها وهو مستني النتيجة) الاتنين
 * بيعتمدوا على endpoint واحد بسيط يرجع حالة السؤال الحالية بصيغة JSON، وكل واحد
 * فيهم بيسأله كل 3 ثواني (نفس دورة الـ polling المستخدمة في الشات تماماً).
 *
 * ليه endpoint واحد مشترك بدل واحد لكل طرف؟ لأن الاتنين محتاجين نفس المعلومة
 * الأساسية فعلياً: "السؤال ده لسه شغال؟ باقي كام ثانية؟ خلص؟" - المحاضر يستخدمها
 * عشان يعرض "كم واحد جاوب لحد الآن"، والطالب يستخدمها عشان يعرف الوقت المتبقي
 * الحقيقي ويعرف امتى يظهرله الجواب الصحيح. فرق مهم: الخيار الصحيح (correct_option)
 * وتوزيع الإجابات ما بيتبعتوش في الرد إلا لو السؤال أصلاً "ended" - عشان طالب لسه
 * بيجاوب ما يقدرش يشوف الحل الصحيح قبل ما الوقت يخلص عن طريق فتح الشبكة (Network tab).
 */
class QuizController extends Controller
{
    public function status(Quiz $quiz)
    {
        $user = auth()->user();

        $this->authorizeAccess($quiz, $user);

        // بنحدّث الحالة هنا لو الوقت خلص فعلاً - نفس نقطة التحديث المستخدمة في كل
        // صفحات الكويز، عشان أي طرف يسأل عن الحالة يشوف "ended" فوراً مش لسه "active"
        $quiz->refreshStatusIfExpired();

        $answeredCount = $quiz->answers()->count();

        $response = [
            'status'            => $quiz->status,
            'seconds_remaining' => $quiz->secondsRemaining(),
            'answered_count'    => $answeredCount,
            'total_students'    => $quiz->eligibleStudentsCount(),
            'correct_option'    => null,
            'option_counts'     => null,
            'correct_percentage' => null,
        ];

        if ($quiz->status === 'ended') {
            $response['correct_option'] = $quiz->correct_option;

            if ($answeredCount > 0) {
                $response['option_counts'] = $quiz->answers()
                    ->selectRaw('selected_option, count(*) as total')
                    ->groupBy('selected_option')
                    ->pluck('total', 'selected_option');

                $response['correct_percentage'] = round(
                    ($quiz->answers()->where('is_correct', true)->count() / $answeredCount) * 100
                );
            }
        }

        return response()->json($response);
    }

    /**
     * هل فيه سؤال شغال دلوقتي لهذا المقرر؟ محتاجينها في صفحة بث الطالب - لو المحاضر
     * أطلق كويز والطالب قاعد يتابع اللايف، لازم يعرف من غير ما يسيب صفحة البث أصلاً
     * (شوف student/stream.blade.php، بتسأل الـ endpoint ده كل 3 ثواني نفس الشات بالظبط)
     */
    public function activeForCourse(CourseOffering $courseOffering)
    {
        $user = auth()->user();

        $this->authorizeCourseAccess($courseOffering, $user);

        $quiz = Quiz::where('course_offering_id', $courseOffering->id)
            ->where('status', 'active')
            ->first();

        $quiz?->refreshStatusIfExpired();

        if ($quiz && $quiz->status !== 'active') {
            $quiz = null;
        }

        return response()->json([
            'active'  => (bool) $quiz,
            'quiz_id' => $quiz->id ?? null,
        ]);
    }

    /**
     * نفس منطق صلاحية الشات بالظبط: المحاضر لازم يكون محاضر هذا المقرر، والطالب
     * لازم يكون من نفس قسمه وسمستره (أو مقرر مشترك بين كل الأقسام)
     */
    private function authorizeCourseAccess(CourseOffering $courseOffering, $user): void
    {
        if ($user->role === 'lecturer' && $courseOffering->lecturer_id === $user->id) {
            return;
        }

        if ($user->role === 'student') {
            $sameDepartment = $courseOffering->department_id === null
                || $courseOffering->department_id === $user->department_id;

            if ($sameDepartment && $courseOffering->semester_id === $user->semester_id) {
                return;
            }
        }

        abort(403, 'غير مصرح لك بالوصول إلى بيانات هذا المقرر.');
    }

    /**
     * نفس منطق الصلاحية المستخدم في صفحات الكويز عند كل طرف: المحاضر لازم يكون
     * صاحب السؤال، والطالب لازم يكون من نفس قسم وسمستر المقرر (أو مقرر مشترك)
     */
    private function authorizeAccess(Quiz $quiz, $user): void
    {
        if ($user->role === 'lecturer' && $quiz->lecturer_id === $user->id) {
            return;
        }

        if ($user->role === 'student') {
            $offering = $quiz->courseOffering;

            $sameDepartment = $offering->department_id === null
                || $offering->department_id === $user->department_id;

            if ($sameDepartment && $offering->semester_id === $user->semester_id) {
                return;
            }
        }

        abort(403, 'غير مصرح لك بالوصول إلى هذا السؤال.');
    }
}
