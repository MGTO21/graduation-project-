@extends('layouts.lecturer')

@section('content')

<h2 class="page-title mb-1">
    مرحباً، {{ $lecturer->name }}
</h2>

<p class="text-muted text-sm mb-10 pr-3">
    لوحة المحاضر
</p>

{{-- بث اليوم: بطاقة بارزة أعلى اللوحة عشان زر بدء البث يبان بوضوح، مش مدفون جوا الجدول الأسبوعي --}}
<h3 class="section-title mb-4">
    بث المحاضرات اليوم
</h3>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-12">

    @forelse($todaySchedules as $schedule)

        <div class="card">

            <p class="font-cairo font-bold text-ink text-sm mb-1">
                {{ $schedule->courseOffering->course->name }}
            </p>

            <p class="text-xs text-muted mb-3">
                {{ $schedule->courseOffering->department->name ?? 'كل الأقسام' }} — {{ $schedule->courseOffering->semester->name }}
                <br>
                {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
            </p>

            @php $todayLecture = $todayLectures->get($schedule->id); @endphp

            @if($todayLecture && $todayLecture->status === 'live')
                <a href="{{ route('lecturer.stream.show', $todayLecture) }}" class="btn-gold w-full text-center block">
                    متابعة البث المباشر
                </a>
            @elseif($todayLecture && $todayLecture->status === 'ended')
                <span class="text-xs text-muted">انتهى بث اليوم لهذا الموعد</span>
            @else
                <form action="{{ route('lecturer.stream.start', $schedule) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-gold w-full">
                        بدء بث مباشر
                    </button>
                </form>
            @endif

        </div>

    @empty
        <div class="card text-center sm:col-span-2 lg:col-span-3">
            <p class="text-muted text-sm">
                لا توجد محاضرات مجدولة اليوم لبدء بث مباشر
            </p>
        </div>
    @endforelse

</div>

{{-- المقررات المطروحة --}}
<h3 class="section-title mb-4">
    مقرراتي المطروحة
</h3>

<table class="table-n mb-12">
    <thead>
        <tr>
            <th>المقرر</th>
            <th>القسم</th>
            <th>السمستر</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
        @forelse($courseOfferings as $offering)
            <tr>
                <td>{{ $offering->course->name }}</td>
                <td>{{ $offering->department->name ?? 'كل الأقسام' }}</td>
                <td>{{ $offering->semester->name }}</td>
                <td>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('lecturer.lectures.index', $offering) }}" class="btn-outline !px-3 !py-1 !text-xs">
                            محاضرات المقرر
                        </a>
                        <a href="{{ route('chat.show', $offering) }}" class="btn-ghost !px-3 !py-1 !text-xs">
                            الشات
                        </a>

                        {{-- بث طارئ: يفتح بث فوري لهذا المقرر حتى لو اليوم ما فيه موعد مجدول --}}
                        @php $liveLecture = $liveLecturesByOffering->get($offering->id); @endphp

                        @if($liveLecture)
                            <a href="{{ route('lecturer.stream.show', $liveLecture) }}" class="btn-gold !px-3 !py-1 !text-xs">
                                متابعة البث
                            </a>
                        @else
                            <form action="{{ route('lecturer.stream.emergency', $offering) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-outline !px-3 !py-1 !text-xs">
                                    بث طارئ
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center !py-8 text-muted">
                    لا توجد مقررات مسندة إليك حالياً
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- الجدول الأسبوعي --}}
<h3 class="section-title mb-4">
    جدولي الأسبوعي
</h3>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">

    @foreach($days as $dayKey => $dayName)

        <div class="border border-line rounded-sm {{ now()->format('l') === $dayKey ? 'day-today' : 'bg-white' }}">

            <div class="px-3 py-2.5 border-b border-line text-center">
                <span class="font-cairo font-bold text-ink text-sm">{{ $dayName }}</span>
                @if(now()->format('l') === $dayKey)
                    <span class="block text-[10px] text-gold">اليوم</span>
                @endif
            </div>

            <div class="p-3 space-y-3">

                @if(isset($schedules[$dayKey]) && $schedules[$dayKey]->count())

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

                            {{-- حالة البث تظهر بس في عمود اليوم الحالي، للمعلومية - زر بدء البث الفعلي فوق في بطاقة "بث المحاضرات اليوم" --}}
                            @if($dayKey === now()->format('l'))
                                @php $todayLecture = $todayLectures->get($schedule->id); @endphp

                                @if($todayLecture && $todayLecture->status === 'live')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-danger mt-1">
                                        <span class="h-1.5 w-1.5 rounded-full bg-danger"></span>
                                        البث مباشر الآن
                                    </span>
                                @elseif($todayLecture && $todayLecture->status === 'ended')
                                    <span class="block text-xs text-muted mt-1">انتهى البث اليوم</span>
                                @endif
                            @endif

                        </div>
                    @endforeach

                @else
                    <p class="text-muted text-xs text-center py-6">
                        لا توجد محاضرات
                    </p>
                @endif

            </div>

        </div>

    @endforeach

</div>

@endsection
