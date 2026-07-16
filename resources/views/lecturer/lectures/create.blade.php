@extends('layouts.lecturer')

@section('content')

<h2 class="page-title mb-8">
    إضافة محاضرة لمقرر: {{ $courseOffering->course->name }}
</h2>

<div class="card max-w-2xl">

    <form action="{{ route('lecturer.lectures.store', $courseOffering) }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf

        <div class="mb-5">
            <label class="input-label">موعد الجدول</label>

            <select name="lecture_schedule_id" class="input-field" required>
                <option value="">-- اختر الموعد --</option>
                @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}"
                        {{ old('lecture_schedule_id') == $schedule->id ? 'selected' : '' }}>
                        {{ $days[$schedule->day] }}
                        {{ substr($schedule->start_time, 0, 5) }}
                        -
                        {{ substr($schedule->end_time, 0, 5) }}
                    </option>
                @endforeach
            </select>

            @error('lecture_schedule_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">تاريخ المحاضرة</label>

            <input
                type="date"
                name="lecture_date"
                value="{{ old('lecture_date') }}"
                class="input-field"
                required>

            @error('lecture_date')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">
                ملفات المحاضرة
                <span class="block mt-1 text-xs text-muted font-normal">
                    فيديو mp4 / صوت mp3 / ملف pdf — بحد أقصى 100MB للملف
                </span>
            </label>

            <input
                type="file"
                name="files[]"
                accept=".mp4,.mp3,.pdf"
                class="input-field !py-2 file:ml-3 file:border-0 file:bg-sand file:text-ink file:text-xs file:font-medium file:px-3 file:py-1.5 file:rounded-sm file:cursor-pointer"
                multiple
                required>

            @error('files')
                <p class="error-text">{{ $message }}</p>
            @enderror

            @error('files.*')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <button class="btn-gold">
                حفظ المحاضرة
            </button>

            <a href="{{ route('lecturer.lectures.index', $courseOffering) }}" class="btn-ghost">
                إلغاء
            </a>
        </div>

    </form>

</div>

@endsection
