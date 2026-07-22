@if($ended)

    {{-- السؤال خلص وقته - نعرض توزيع الإجابات مباشرة من غير ما نستنى الـ JS يجيبها --}}
    <p class="font-cairo font-bold text-ink mb-3">انتهى وقت السؤال</p>

    <div class="mb-4">
        @foreach(['a' => 'أ', 'b' => 'ب', 'c' => 'ج', 'd' => 'د'] as $key => $label)
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="{{ $quiz->correct_option === $key ? 'text-success font-bold' : 'text-body' }}">
                    {{ $label }} {{ $quiz->correct_option === $key ? '(الصحيحة)' : '' }}
                </span>
                <span class="font-cairo font-bold">{{ $optionCounts[$key] ?? 0 }}</span>
            </div>
        @endforeach
    </div>

    <p class="text-sm text-body">
        نسبة الإجابات الصحيحة:
        <span class="font-cairo font-bold text-gold">{{ $correctPercentage ?? 0 }}%</span>
    </p>

@else

    {{-- السؤال شغال دلوقتي - الـ JS في الصفحة الأصلية هيحدّث الأرقام دي كل 3 ثواني --}}
    <p class="text-sm text-body mb-2">
        جاوب لحد الآن <span class="font-cairo font-bold text-gold">{{ $answeredCount }}</span>
        من أصل <span class="font-cairo font-bold">{{ $totalStudents }}</span> طالب
    </p>

    <div class="w-full bg-sand rounded-sm h-2 overflow-hidden">
        <div class="bg-gold h-full" style="width: {{ $totalStudents > 0 ? round(($answeredCount / $totalStudents) * 100) : 0 }}%"></div>
    </div>

@endif
