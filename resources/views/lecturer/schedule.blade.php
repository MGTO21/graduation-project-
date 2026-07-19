@extends('layouts.lecturer')

@section('content')

<h2 class="page-title mb-8">
    جدولي الأسبوعي
</h2>

{{-- الأيام بالطول: كل يوم صف كامل العرض، مواعيده جنب بعض جواه - بدل ما تكون الأيام أعمدة جنب بعض --}}
<div class="space-y-4">

    @foreach($days as $dayKey => $dayName)

        <div class="border border-line rounded-sm {{ now()->format('l') === $dayKey ? 'day-today' : 'bg-white' }}">

            <div class="px-4 py-2.5 border-b border-line flex items-center gap-2">
                <span class="font-cairo font-bold text-ink">{{ $dayName }}</span>
                @if(now()->format('l') === $dayKey)
                    <span class="text-[10px] text-gold">اليوم</span>
                @endif
            </div>

            <div class="p-4">

                @if(isset($schedules[$dayKey]) && $schedules[$dayKey]->count())

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($schedules[$dayKey] as $schedule)
                            <div class="slot-card">

                                <p class="font-cairo font-bold text-ink text-sm mb-1">
                                    {{ $schedule->courseOffering->course->name }}
                                </p>

                                <p class="text-xs text-muted mb-1">
                                    {{ $schedule->courseOffering->department->name ?? 'كل الأقسام' }} — {{ $schedule->courseOffering->semester->name }}
                                </p>

                                <p class="text-xs font-medium text-gold">
                                    {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                </p>

                            </div>
                        @endforeach
                    </div>

                @else
                    <p class="text-muted text-xs text-center py-4">
                        لا توجد محاضرات
                    </p>
                @endif

            </div>

        </div>

    @endforeach

</div>

@endsection
