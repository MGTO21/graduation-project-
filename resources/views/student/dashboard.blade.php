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

<table class="table-n">
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

@endsection
