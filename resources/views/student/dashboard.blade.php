@extends('layouts.student')

@section('content')

<h2 class="page-title mb-1">
    مرحباً، {{ $student->name }}
</h2>

<p class="text-muted text-sm mb-10 pr-3">
    القسم: {{ $student->department->name ?? '-' }}
    |
    السمستر: {{ $student->semester->name ?? '-' }}
</p>

{{-- بطاقة بارزة تظهر بس لو في محاضرة مباشرة الآن لأحد مقررات الطالب --}}
@if($liveLecture)
    <div class="card !border-danger !border-2 mb-10 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-danger opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-danger"></span>
            </span>
            <div>
                <p class="font-cairo font-bold text-ink">
                    محاضرة مباشرة الآن: {{ $liveLecture->lectureSchedule->courseOffering->course->name }}
                </p>
                <p class="text-xs text-muted">اضغط للدخول ومتابعة البث والمشاركة في الشات</p>
            </div>
        </div>

        <a href="{{ route('student.stream.watch', $liveLecture) }}" class="btn-gold">
            دخول البث
        </a>
    </div>
@endif

{{-- مقررات السمستر الحالي --}}
<h3 class="section-title mb-4">
    مقررات السمستر الحالي
</h3>

<table class="table-n mb-12">
    <thead>
        <tr>
            <th>رمز المقرر</th>
            <th>اسم المقرر</th>
            <th>المحاضر</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
        @forelse($courseOfferings as $offering)
            <tr>
                <td>{{ $offering->course->code }}</td>
                <td>{{ $offering->course->name }}</td>
                <td>{{ $offering->lecturer->name }}</td>
                <td>
                    <a href="{{ route('chat.show', $offering) }}" class="btn-ghost !px-3 !py-1 !text-xs">
                        الشات
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center !py-8 text-muted">
                    لا توجد مقررات مسجلة لقسمك وسمسترك حالياً
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- الجدول الأسبوعي --}}
<h3 class="section-title mb-4">
    جدولي الأسبوعي
</h3>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-12">

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
                                {{ $schedule->courseOffering->lecturer->name }}
                            </p>

                            <p class="text-xs font-medium text-gold">
                                {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                            </p>

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

{{-- المحاضرات المرفوعة --}}
<h3 class="section-title mb-4">
    المحاضرات المرفوعة
</h3>

<table class="table-n">
    <thead>
        <tr>
            <th>المقرر</th>
            <th>المحاضر</th>
            <th>تاريخ المحاضرة</th>
            <th>عدد الملفات</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
        @forelse($lectures as $lecture)
            <tr>
                <td>{{ $lecture->lectureSchedule->courseOffering->course->name }}</td>
                <td>{{ $lecture->lectureSchedule->courseOffering->lecturer->name }}</td>
                <td>{{ $lecture->lecture_date }}</td>
                <td>{{ $lecture->files->count() }}</td>
                <td>
                    <a href="{{ route('student.lectures.show', $lecture) }}" class="btn-outline !px-3 !py-1 !text-xs">
                        مشاهدة المحاضرة
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center !py-8 text-muted">
                    لا توجد محاضرات مرفوعة بعد
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- المحاضرات المباشرة التي انتهت (بثوث سابقة) --}}
<h3 class="section-title mb-4 mt-12">
    محاضرات سابقة
</h3>

<table class="table-n">
    <thead>
        <tr>
            <th>المقرر</th>
            <th>المحاضر</th>
            <th>تاريخ المحاضرة</th>
            <th>عدد الملفات</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
        @forelse($pastLectures as $lecture)
            <tr>
                <td>{{ $lecture->lectureSchedule->courseOffering->course->name }}</td>
                <td>{{ $lecture->lectureSchedule->courseOffering->lecturer->name }}</td>
                <td>{{ $lecture->lecture_date }}</td>
                <td>{{ $lecture->files->count() }}</td>
                <td>
                    <a href="{{ route('student.lectures.show', $lecture) }}" class="btn-outline !px-3 !py-1 !text-xs">
                        عرض المحاضرة
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center !py-8 text-muted">
                    لا توجد محاضرات سابقة بعد
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
