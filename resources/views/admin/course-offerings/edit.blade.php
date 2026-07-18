@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    تعديل طرح مقرر
</h2>

<div class="card max-w-2xl">

    <form action="{{ route('admin.course-offerings.update', $courseOffering) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="input-label">المقرر</label>

            <select name="course_id" class="input-field" required>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ $courseOffering->course_id == $course->id ? 'selected' : '' }}>
                        {{ $course->code }} - {{ $course->name }}
                    </option>
                @endforeach
            </select>

            @error('course_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">القسم</label>

            <select name="department_id" class="input-field">
                <option value="" {{ is_null($courseOffering->department_id) ? 'selected' : '' }}>
                    كل الأقسام (مادة مشتركة)
                </option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}"
                        {{ $courseOffering->department_id == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>

            <p class="text-xs text-muted mt-1.5">اختر "كل الأقسام" لو المقرر عام ومشترك بين كل الأقسام.</p>

            @error('department_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">السمستر</label>

            <select name="semester_id" class="input-field" required>
                @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}"
                        {{ $courseOffering->semester_id == $semester->id ? 'selected' : '' }}>
                        {{ $semester->name }}
                    </option>
                @endforeach
            </select>

            @error('semester_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">المحاضر</label>

            <select name="lecturer_id" class="input-field" required>
                @foreach($lecturers as $lecturer)
                    <option value="{{ $lecturer->id }}"
                        {{ $courseOffering->lecturer_id == $lecturer->id ? 'selected' : '' }}>
                        {{ $lecturer->name }}
                    </option>
                @endforeach
            </select>

            @error('lecturer_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn-gold">
            تحديث
        </button>

    </form>

</div>

@endsection
