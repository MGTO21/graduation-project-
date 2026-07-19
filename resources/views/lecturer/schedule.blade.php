@extends('layouts.lecturer')

@section('content')

<h2 class="page-title mb-8">
    جدولي الأسبوعي
</h2>

{{-- عمود واحد لليوم، وقصاده في نفس الصف كل محاضرات اليوم ده مع بعض --}}
<div class="overflow-x-auto">
    <table class="table-n">
        <thead>
            <tr>
                <th class="w-32">اليوم</th>
                <th>المحاضرات</th>
            </tr>
        </thead>

        <tbody>
            @foreach($days as $dayKey => $dayName)
                <tr class="{{ now()->format('l') === $dayKey ? 'day-today' : '' }} align-top">
                    <td>
                        <span class="font-cairo font-bold text-ink">{{ $dayName }}</span>
                        @if(now()->format('l') === $dayKey)
                            <span class="block text-[10px] text-gold mt-0.5">اليوم</span>
                        @endif
                    </td>
                    <td>
                        @if(isset($schedules[$dayKey]) && $schedules[$dayKey]->count())
                            <div class="flex flex-wrap gap-3">
                                @foreach($schedules[$dayKey] as $schedule)
                                    <div class="slot-card min-w-[220px]">

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
                            <span class="text-muted text-xs">لا توجد محاضرات</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
