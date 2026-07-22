<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class QuizController extends Controller
{
    /**
     * صفحة الإجابة على سؤال: تعرض نص السؤال والخيارات مع مؤقت تنازلي، أو نتيجة
     * الطالب لو أصلاً جاوب أو خلص الوقت
     */
    public function show(Quiz $quiz)
    {
        $student = auth()->user();

        $this->authorizeStudent($quiz, $student);

        // السؤال لسه draft يعني المحاضر ما أطلقه بعد - ما فيه داعي الطالب يشوفه أصلاً
        if ($quiz->status === 'draft') {
            abort(403, 'هذا السؤال لم يُطلق بعد.');
        }

        $quiz->refreshStatusIfExpired();
        $quiz->load('courseOffering.course');

        $myAnswer = QuizAnswer::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->first();

        return view('student.quizzes.show', compact('quiz', 'myAnswer'));
    }

    /**
     * حفظ إجابة الطالب على السؤال
     */
    public function store(Request $request, Quiz $quiz)
    {
        $student = auth()->user();

        $this->authorizeStudent($quiz, $student);

        $request->validate([
            'selected_option' => 'required|in:a,b,c,d',
        ]);

        // نتأكد الحالة فعلياً active ومفيش وقت خلص - ممكن الطالب يبعت الطلب في
        // اللحظة الأخيرة والوقت يكون خلص فعلاً من ناحية السيرفر حتى لو الجافاسكريبت
        // عنده لسه ما وصلش صفر (فرق بسيط بين توقيت المتصفح والسيرفر أو تأخير الشبكة)
        $quiz->refreshStatusIfExpired();

        if ($quiz->status !== 'active') {
            return response()->json(['message' => 'انتهى وقت الإجابة على هذا السؤال.'], 422);
        }

        // خط الدفاع الثاني (بعد قيد unique في القاعدة): نتحقق هنا برضو عشان نقدر
        // نرجع رسالة عربية واضحة بدل ما يطلع للطالب خطأ قاعدة بيانات خام
        if (QuizAnswer::where('quiz_id', $quiz->id)->where('student_id', $student->id)->exists()) {
            return response()->json(['message' => 'أنت أجبت على هذا السؤال من قبل.'], 422);
        }

        try {
            QuizAnswer::create([
                'quiz_id'         => $quiz->id,
                'student_id'      => $student->id,
                'selected_option' => $request->selected_option,
                'is_correct'      => $request->selected_option === $quiz->correct_option,
                'answered_at'     => now(),
            ]);
        } catch (QueryException $e) {
            // لو وصلنا هنا معناها طلبين اتبعتوا من نفس الطالب في نفس اللحظة تقريباً
            // (مثلاً دبل-كليك) وعدّوا كل التحققات فوق قبل ما أي واحد فيهم يخلص -
            // قيد unique في القاعدة هو اللي مسك الحالة دي فعلياً ورفض التكرار
            return response()->json(['message' => 'أنت أجبت على هذا السؤال من قبل.'], 422);
        }

        return response()->json(['success' => true]);
    }

    /**
     * سجل كل الأسئلة اللي الطالب جاوب عليها من قبل مع نتيجته في كل واحد
     */
    public function history()
    {
        $student = auth()->user();

        $answers = QuizAnswer::with(['quiz.courseOffering.course'])
            ->where('student_id', $student->id)
            ->orderByDesc('answered_at')
            ->get();

        return view('student.quizzes.history', compact('answers'));
    }

    /**
     * حماية: الطالب لا يصل إلا لأسئلة مقررات قسمه وسمستره (أو مقرر مشترك بين كل الأقسام)
     */
    private function authorizeStudent(Quiz $quiz, $student): void
    {
        $offering = $quiz->courseOffering;

        $sameDepartment = $offering->department_id === null
            || $offering->department_id === $student->department_id;

        if (! $sameDepartment || $offering->semester_id !== $student->semester_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا السؤال.');
        }
    }
}
