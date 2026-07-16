@extends('layouts.student')

@section('content')

<div class="flex items-center justify-between mb-2">
    <h2 class="page-title">
        محاضرة مقرر: {{ $lecture->lectureSchedule->courseOffering->course->name }}
    </h2>

    <a href="{{ route('student.dashboard') }}" class="btn-ghost">
        رجوع للوحة
    </a>
</div>

<p class="text-muted text-sm mb-8 pr-3">
    المحاضر: {{ $lecture->lectureSchedule->courseOffering->lecturer->name }}
    |
    التاريخ: {{ $lecture->lecture_date }}
</p>

<div class="space-y-6">

    @forelse($lecture->files as $file)

        <div class="card">

            <p class="font-cairo font-bold text-ink mb-4 pb-3 border-b border-line">
                {{ $file->original_name }}
                <span class="text-muted text-xs font-tajawal font-normal">
                    ({{ round($file->file_size / 1048576, 2) }} MB)
                </span>
            </p>

            @if($file->file_type === 'mp4')

                {{-- تشغيل الفيديو داخل الصفحة --}}
                <video controls class="w-full rounded-sm" preload="metadata">
                    <source src="{{ asset('storage/' . $file->file_path) }}" type="video/mp4">
                    متصفحك لا يدعم تشغيل الفيديو
                </video>

            @elseif($file->file_type === 'mp3')

                {{-- تشغيل الصوت داخل الصفحة --}}
                <audio controls class="w-full" preload="metadata">
                    <source src="{{ asset('storage/' . $file->file_path) }}" type="audio/mpeg">
                    متصفحك لا يدعم تشغيل الصوت
                </audio>

            @else

                {{-- ملف pdf: عرض أو تحميل --}}
                <div class="flex items-center gap-2">
                    <a href="{{ asset('storage/' . $file->file_path) }}"
                        target="_blank"
                        class="btn-gold">
                        عرض الملف
                    </a>

                    <a href="{{ asset('storage/' . $file->file_path) }}"
                        download="{{ $file->original_name }}"
                        class="btn-outline">
                        تحميل
                    </a>
                </div>

            @endif

        </div>

    @empty
        <div class="card text-center">
            <p class="text-muted">
                لا توجد ملفات لهذه المحاضرة
            </p>
        </div>
    @endforelse

</div>

@endsection
