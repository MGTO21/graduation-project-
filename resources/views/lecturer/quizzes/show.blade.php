@extends('layouts.lecturer')

@section('content')

<div class="flex items-center justify-between mb-2">
    <h2 class="page-title">
        سؤال فوري: {{ $quiz->courseOffering->course->name }}
    </h2>

    <a href="{{ route('lecturer.quizzes.index', $quiz->courseOffering) }}" class="btn-ghost">
        رجوع لسجل الأسئلة
    </a>
</div>

<div class="card max-w-2xl">

    <p class="font-cairo font-bold text-ink mb-4 pb-3 border-b border-line">
        {{ $quiz->question }}
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
        @foreach(['a' => 'أ', 'b' => 'ب', 'c' => 'ج', 'd' => 'د'] as $key => $label)
            <div class="slot-card {{ $quiz->correct_option === $key ? '!border-r-success' : '' }}">
                <span class="font-cairo font-bold text-xs text-muted">{{ $label }}.</span>
                {{ $quiz->{'option_' . $key} }}
                @if($quiz->correct_option === $key)
                    <span class="text-success text-xs mr-1">(الإجابة الصحيحة)</span>
                @endif
            </div>
        @endforeach
    </div>

    @if($quiz->status === 'draft')

        {{-- لسه مسودة: المحاضر يقدر يراجع السؤال قبل ما يطلقه --}}
        <form action="{{ route('lecturer.quizzes.launch', $quiz) }}" method="POST">
            @csrf
            <button class="btn-gold w-full" onclick="return confirm('هل أنت متأكد من إطلاق السؤال؟ من هذه اللحظة سيظهر للطلاب.')">
                إطلاق السؤال الآن
            </button>
        </form>

    @else

        {{-- شغال أو خلص - المنطقة دي بتتحدث لحالها عن طريق JS لو السؤال شغال --}}
        <div id="quiz-live-area"
             data-status-url="{{ route('quizzes.status', $quiz) }}"
             data-initial-status="{{ $quiz->status }}">

            @include('lecturer.quizzes._live-section', [
                'answeredCount' => $answeredCount,
                'totalStudents' => $totalStudents,
                'optionCounts' => $optionCounts,
                'correctPercentage' => $correctPercentage,
                'ended' => $quiz->status === 'ended',
            ])

        </div>

    @endif

</div>

@if($quiz->status === 'active')
<script>
/*
 * نفس فكرة polling الشات بالظبط: كل 3 ثواني نسأل السيرفر عن حالة السؤال عن طريق
 * endpoint واحد مشترك بين المحاضر والطالب (quizzes.status). طول ما السؤال active
 * بنعرض بس عدد اللي جاوبوا لحد الآن، وأول ما الرد يرجع status=ended (يعني وقته خلص
 * فعلياً من ناحية السيرفر) نستبدل المحتوى بتوزيع الإجابات النهائي من غير ما نعيد
 * تحميل الصفحة، ونوقف الـ polling لأنه ما فيه داعي نكمل نسأل بعد كده.
 */
(function () {
    const area = document.getElementById('quiz-live-area');
    const statusUrl = area.dataset.statusUrl;

    const optionLabels = { a: 'أ', b: 'ب', c: 'ج', d: 'د' };

    function renderLive(data) {
        area.innerHTML = `
            <p class="text-sm text-body mb-2">
                جاوب لحد الآن <span class="font-cairo font-bold text-gold">${data.answered_count}</span>
                من أصل <span class="font-cairo font-bold">${data.total_students}</span> طالب
            </p>
            <div class="w-full bg-sand rounded-sm h-2 overflow-hidden">
                <div class="bg-gold h-full" style="width: ${data.total_students > 0 ? (data.answered_count / data.total_students) * 100 : 0}%"></div>
            </div>
            <p class="text-xs text-muted mt-2">الوقت المتبقي: ${data.seconds_remaining} ثانية</p>
        `;
    }

    function renderEnded(data) {
        let rowsHtml = '';
        for (const key of ['a', 'b', 'c', 'd']) {
            const count = (data.option_counts && data.option_counts[key]) || 0;
            rowsHtml += `
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="${key === data.correct_option ? 'text-success font-bold' : 'text-body'}">
                        ${optionLabels[key]} ${key === data.correct_option ? '(الصحيحة)' : ''}
                    </span>
                    <span class="font-cairo font-bold">${count}</span>
                </div>
            `;
        }

        area.innerHTML = `
            <p class="font-cairo font-bold text-ink mb-3">انتهى وقت السؤال</p>
            <div class="mb-4">${rowsHtml}</div>
            <p class="text-sm text-body">
                نسبة الإجابات الصحيحة:
                <span class="font-cairo font-bold text-gold">${data.correct_percentage ?? 0}%</span>
            </p>
        `;
    }

    const interval = setInterval(function () {
        fetch(statusUrl)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ended') {
                    renderEnded(data);
                    clearInterval(interval);
                } else {
                    renderLive(data);
                }
            })
            .catch(() => {
                // خطأ شبكة عابر، ننتظر المحاولة الجاية
            });
    }, 3000);
})();
</script>
@endif

@endsection
