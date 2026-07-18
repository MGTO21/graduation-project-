@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    تعديل الموعد
</h2>

<div class="card max-w-2xl">

    <form action="{{ route('admin.lecture-schedules.update', $lectureSchedule) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="input-label">المقرر المطروح</label>

            <select name="course_offering_id" class="input-field" required>
                @foreach($courseOfferings as $offering)
                    <option value="{{ $offering->id }}"
                        {{ old('course_offering_id', $lectureSchedule->course_offering_id) == $offering->id ? 'selected' : '' }}>
                        {{ $offering->course->name }} - {{ $offering->department->name ?? 'كل الأقسام' }} - {{ $offering->semester->name }} - {{ $offering->lecturer->name }}
                    </option>
                @endforeach
            </select>

            @error('course_offering_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">اليوم</label>

            <select name="day" class="input-field" required>
                @foreach($days as $dayKey => $dayName)
                    <option value="{{ $dayKey }}"
                        {{ old('day', $lectureSchedule->day) == $dayKey ? 'selected' : '' }}>
                        {{ $dayName }}
                    </option>
                @endforeach
            </select>

            @error('day')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">وقت البداية</label>

            <input
                type="time"
                name="start_time"
                value="{{ old('start_time', substr($lectureSchedule->start_time, 0, 5)) }}"
                class="input-field"
                required>

            @error('start_time')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">وقت النهاية</label>

            <input
                type="time"
                name="end_time"
                value="{{ old('end_time', substr($lectureSchedule->end_time, 0, 5)) }}"
                class="input-field"
                required>

            @error('end_time')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn-gold">
            حفظ التعديلات
        </button>

    </form>

</div>

@endsection
