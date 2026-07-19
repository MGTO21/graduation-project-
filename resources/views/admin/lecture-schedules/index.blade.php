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

{{-- عمود واحد لليوم، وقصاده في نفس الصف كل مواعيد اليوم ده مع بعض --}}
<div class="overflow-x-auto">
    <table class="table-n">
        <thead>
            <tr>
                <th class="w-32">اليوم</th>
                <th>المواعيد</th>
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
                            <span class="text-muted text-xs">لا توجد مواعيد</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
