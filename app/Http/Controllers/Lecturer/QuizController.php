<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * سجل كل الأسئلة السابقة لهذا المقرر (لا حذف - يبقى للمراجعة والمناقشة)
     */
    public function index(CourseOffering $courseOffering)
    {
        if ($courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى اختبارات هذا المقرر.');
        }

        $quizzes = Quiz::where('course_offering_id', $courseOffering->id)
            ->withCount('answers')
            ->orderByDesc('created_at')
            ->get();

        return view('lecturer.quizzes.index', compact('courseOffering', 'quizzes'));
    }

    /**
     * نموذج إنشاء سؤال جديد (بيتحفظ draft لحد ما المحاضر يضغط "إطلاق")
     */
    public function create(CourseOffering $courseOffering)
    {
        if ($courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى اختبارات هذا المقرر.');
        }

        return view('lecturer.quizzes.create', compact('courseOffering'));
    }

    /**
     * حفظ السؤال بحالة draft - لسه ما ظهرش للطلاب لحد ما يتضغط "إطلاق السؤال الآن"
     */
    public function store(Request $request, CourseOffering $courseOffering)
    {
        if ($courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى اختبارات هذا المقرر.');
        }

        $request->validate([
            'question'         => 'required|string|max:1000',
            'option_a'         => 'required|string|max:255',
            'option_b'         => 'required|string|max:255',
            'option_c'         => 'required|string|max:255',
            'option_d'         => 'required|string|max:255',
            'correct_option'   => 'required|in:a,b,c,d',
            'duration_seconds' => 'required|integer|min:10|max:600',
        ]);

        $quiz = Quiz::create([
            'course_offering_id' => $courseOffering->id,
            'lecturer_id'        => auth()->id(),
            'question'           => $request->question,
            'option_a'           => $request->option_a,
            'option_b'           => $request->option_b,
            'option_c'           => $request->option_c,
            'option_d'           => $request->option_d,
            'correct_option'     => $request->correct_option,
            'duration_seconds'   => $request->duration_seconds,
            'status'             => 'draft',
        ]);

        return redirect()->route('lecturer.quizzes.show', $quiz);
    }

    /**
     * إطلاق السؤال: من هذه اللحظة الطلاب يشوفوه ويقدروا يجاوبوا عليه
     */
    public function launch(Quiz $quiz)
    {
        if ($quiz->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالتحكم في هذا السؤال.');
        }

        if ($quiz->status !== 'draft') {
            return back()->with('error', 'هذا السؤال أصلاً تم إطلاقه من قبل.');
        }

        $quiz->update([
            'status'     => 'active',
            'started_at' => now(),
        ]);

        return redirect()->route('lecturer.quizzes.show', $quiz);
    }

    /**
     * صفحة السؤال الموحدة عند المحاضر: تتغير حسب حالته -
     * draft: معاينة + زر إطلاق، active: متابعة حية (عدد المجاوبين)، ended: توزيع النتائج
     */
    public function show(Quiz $quiz)
    {
        if ($quiz->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا السؤال.');
        }

        // لو الوقت خلص فعلياً بس الحالة لسه active في القاعدة (محدش فتح الصفحة من
        // وقت الانتهاء)، نحدّثها دلوقتي - نفس فكرة أي polling، بنحدّث الحالة أول ما
        // حد يسأل عنها بدل ما نحتاج job مجدول يدور على الأسئلة المنتهية
        $quiz->refreshStatusIfExpired();

        $quiz->load('courseOffering.course');

        $totalStudents = $quiz->eligibleStudentsCount();
        $answeredCount = $quiz->answers()->count();

        // توزيع الإجابات - بس لو خلص الوقت، ما فايدة نعرضها والسؤال لسه شغال
        $optionCounts = null;
        $correctPercentage = null;

        if ($quiz->status === 'ended' && $answeredCount > 0) {
            $optionCounts = $quiz->answers()
                ->selectRaw('selected_option, count(*) as total')
                ->groupBy('selected_option')
                ->pluck('total', 'selected_option');

            $correctPercentage = round(($quiz->answers()->where('is_correct', true)->count() / $answeredCount) * 100);
        }

        return view('lecturer.quizzes.show', compact(
            'quiz', 'totalStudents', 'answeredCount', 'optionCounts', 'correctPercentage'
        ));
    }
}
