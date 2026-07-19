@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h2 class="page-title">
        الجدول الأسبوعي للمحاضرات
    </h2>

    <a href="{{ route('admin.lecture-schedules.create') }}" class="btn-gold">
        إضافة موعد
    </a>
</div>

{{-- تبويب السمسترات: كل سمستر جدوله يتعرض لوحده، بدل ما كل السمسترات تتخلط في نفس الشبكة --}}
<div class="flex flex-wrap gap-2 mb-8 pb-4 border-b border-line">
    @forelse($semesters as $semester)
        <a href="{{ route('admin.lecture-schedules.index', ['semester_id' => $semester->id]) }}"
           class="{{ $selectedSemesterId === $semester->id ? 'btn-gold' : 'btn-outline' }} !px-4 !py-1.5 !text-xs">
            {{ $semester->name }}
        </a>
    @empty
        <p class="text-muted text-sm">لا توجد سمسترات مضافة بعد</p>
    @endforelse
</div>

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

                                <p class="text-xs text-muted mb-0.5">
                                    {{ $schedule->courseOffering->department->name ?? 'كل الأقسام' }}
                                </p>

                                <p class="text-xs text-body mb-1">
                                    {{ $schedule->courseOffering->lecturer->name }}
                                </p>

                                <p class="text-xs font-medium text-gold mb-2">
                                    {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                </p>

                                <div class="flex items-center gap-2 pt-2 border-t border-line">

                                    <a href="{{ route('admin.lecture-schedules.edit', $schedule) }}" class="btn-edit">
                                        تعديل
                                    </a>

                                    <form action="{{ route('admin.lecture-schedules.destroy', $schedule) }}"
                                        method="POST"
                                        class="inline">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            onclick="return confirm('هل أنت متأكد من حذف الموعد؟')"
                                            class="btn-del">
                                            حذف
                                        </button>

                                    </form>

                                </div>

                            </div>
                        @endforeach
                    </div>

                @else
                    <p class="text-muted text-xs text-center py-4">
                        لا توجد مواعيد
                    </p>
                @endif

            </div>

        </div>

    @endforeach

</div>

@endsection
