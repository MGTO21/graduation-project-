@extends('layouts.student')

@section('content')

<div class="flex items-center justify-between mb-2">
    <h2 class="page-title">
        سؤال فوري: {{ $quiz->courseOffering->course->name }}
    </h2>

    <a href="{{ route('student.dashboard') }}" class="btn-ghost">
        رجوع للوحة
    </a>
</div>

<p class="text-muted text-sm mb-8 pr-3">
    المحاضر: {{ $quiz->courseOffering->lecturer->name }}
</p>

<div class="card max-w-xl mx-auto text-center"
     id="quiz-area"
     data-status-url="{{ route('quizzes.status', $quiz) }}"
     data-answer-url="{{ route('student.quizzes.answer', $quiz) }}"
     data-duration="{{ $quiz->duration_seconds }}"
     data-initial-remaining="{{ $quiz->secondsRemaining() }}"
     data-quiz-status="{{ $quiz->status }}"
     data-my-selected="{{ $myAnswer->selected_option ?? '' }}"
     data-correct-option="{{ $quiz->status === 'ended' ? $quiz->correct_option : '' }}">

    {{-- المؤقت الدائري: دائرتين SVG فوق بعض، الخارجية خلفية رمادية والداخلية بتتقص
         تدريجياً (stroke-dashoffset) على قد الوقت المتبقي فعلياً من السيرفر --}}
    <div class="relative w-24 h-24 mx-auto mb-6">
        <svg viewBox="0 0 100 100" class="w-24 h-24 -rotate-90">
            <circle cx="50" cy="50" r="45" fill="none" stroke="#E8E2D6" stroke-width="8"></circle>
            <circle id="timer-ring" cx="50" cy="50" r="45" fill="none" stroke="#C8922A" stroke-width="8"
                    stroke-dasharray="282.6" stroke-dashoffset="0" stroke-linecap="round"></circle>
        </svg>
        <span id="timer-text" class="absolute inset-0 flex items-center justify-center font-cairo font-bold text-xl text-ink"></span>
    </div>

    <p class="font-cairo font-bold text-lg text-ink mb-6">
        {{ $quiz->question }}
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4" id="quiz-options">
        @foreach(['a' => 'أ', 'b' => 'ب', 'c' => 'ج', 'd' => 'د'] as $key => $label)
            <button type="button"
                    class="quiz-option-btn slot-card text-right transition-colors"
                    data-option="{{ $key }}"
                    {{ $myAnswer || $quiz->status === 'ended' ? 'disabled' : '' }}>
                <span class="font-cairo font-bold text-xs text-muted">{{ $label }}.</span>
                {{ $quiz->{'option_' . $key} }}
            </button>
        @endforeach
    </div>

    <p id="quiz-message" class="text-sm text-muted"></p>

</div>

<script>
(function () {
    const area = document.getElementById('quiz-area');
    const statusUrl = area.dataset.statusUrl;
    const answerUrl = area.dataset.answerUrl;
    const duration = parseInt(area.dataset.duration, 10);

    const ring = document.getElementById('timer-ring');
    const timerText = document.getElementById('timer-text');
    const message = document.getElementById('quiz-message');
    const buttons = [...document.querySelectorAll('.quiz-option-btn')];

    const circumference = 282.6; // 2 * π * 45، محيط الدائرة اللي رسمناها بالـ SVG

    // بنخزن الوقت المتبقي كـ "لحظة سيرفر مرجعية" وقت ما الصفحة اتفتحت، مش عداد
    // يبدأ من الصفر عندنا - لو الطالب فتح الصفحة متأخر (مثلاً بعد 20 ثانية من
    // الإطلاق) هيشوف الوقت الصحيح المتبقي مباشرة، مش المدة الكاملة من جديد
    let remaining = parseInt(area.dataset.initialRemaining, 10);
    let quizStatus = area.dataset.quizStatus;
    let mySelected = area.dataset.mySelected || null;
    let hasAnswered = !!mySelected;

    function updateRing() {
        const ratio = duration > 0 ? Math.max(0, remaining) / duration : 0;
        ring.style.strokeDashoffset = circumference * (1 - ratio);
        timerText.textContent = Math.max(0, remaining);
    }

    function revealAnswer(correctOption) {
        buttons.forEach(btn => {
            btn.disabled = true;
            const opt = btn.dataset.option;

            if (opt === correctOption) {
                btn.classList.add('!border-r-success', 'bg-success/10');
            } else if (opt === mySelected) {
                btn.classList.add('!border-r-danger', 'bg-danger/10');
            }
        });

        if (! hasAnswered) {
            message.textContent = 'انتهى الوقت ولم تُجب على هذا السؤال.';
        } else if (mySelected === correctOption) {
            message.textContent = 'إجابتك صحيحة!';
            message.className = 'text-sm text-success font-medium';
        } else {
            message.textContent = 'إجابتك غير صحيحة.';
            message.className = 'text-sm text-danger font-medium';
        }
    }

    function lockButtonsWaiting() {
        buttons.forEach(btn => btn.disabled = true);
        message.textContent = 'تم إرسال إجابتك، بانتظار انتهاء الوقت لعرض النتيجة...';
    }

    // لو أصلاً السؤال خلص أو الطالب جاوب قبل كده وقت ما فتح الصفحة، نعرض الحالة
    // النهائية على طول من غير ما نستنى أي polling
    if (quizStatus === 'ended') {
        revealAnswer(area.dataset.correctOption);
    } else if (hasAnswered) {
        lockButtonsWaiting();
    }

    updateRing();

    // عداد بصري بسيط ينزل ثانية كل ثانية، بس هو مجرد عرض - المصدر الحقيقي للوقت
    // هو رد السيرفر عن طريق الـ polling تحت، عشان لو ساعة المتصفح مش مظبوطة أو
    // المستخدم سرح شوية، الرقم يرجع يتصحح كل 3 ثواني من عند السيرفر مش من عندنا
    const tickInterval = setInterval(function () {
        if (quizStatus === 'ended') {
            clearInterval(tickInterval);
            return;
        }
        remaining = Math.max(0, remaining - 1);
        updateRing();
    }, 1000);

    // الضغط على أي زر يبعت الإجابة فوراً ويقفل كل الأزرار - القفل ده مجرد تحسين
    // لتجربة الاستخدام (عشان الطالب ما يحس إنه يقدر يغير رأيه)، الحماية الحقيقية
    // من التكرار موجودة في الكونترولر وفي قيد unique على مستوى القاعدة نفسها
    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            if (hasAnswered || quizStatus === 'ended') return;

            buttons.forEach(b => b.disabled = true);
            mySelected = btn.dataset.option;
            hasAnswered = true;

            const token = document.querySelector('meta[name="csrf-token"]').content;

            fetch(answerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ selected_option: mySelected }),
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (! ok) {
                    // فشل التحقق في السيرفر (مثلاً الوقت خلص فعلاً من ناحيته رغم إن
                    // العداد عندنا لسه ما وصلش صفر) - نعرض رسالته ونسيب الـ polling
                    // يصحح الحالة
                    message.textContent = data.message || 'تعذر إرسال الإجابة.';
                    message.className = 'text-sm text-danger';
                    return;
                }

                lockButtonsWaiting();
            })
            .catch(() => {
                message.textContent = 'تعذر الاتصال بالسيرفر، حاول تاني.';
            });
        });
    });

    // نفس مبدأ polling الشات: كل 3 ثواني نسأل endpoint الحالة المشترك، ونصحح
    // الوقت المتبقي والحالة منه. أول ما يرجع ended نوقف كل شيء ونظهر النتيجة
    const pollInterval = setInterval(function () {
        fetch(statusUrl)
            .then(res => res.json())
            .then(data => {
                remaining = data.seconds_remaining;
                updateRing();

                if (data.status === 'ended') {
                    quizStatus = 'ended';
                    clearInterval(pollInterval);
                    clearInterval(tickInterval);
                    revealAnswer(data.correct_option);
                }
            })
            .catch(() => {
                // خطأ شبكة عابر، ننتظر المحاولة الجاية
            });
    }, 3000);
})();
</script>

@endsection
