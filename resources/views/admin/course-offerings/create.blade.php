@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    إضافة طرح مقرر
</h2>

<div class="card max-w-2xl">

    <form action="{{ route('admin.course-offerings.store') }}" method="POST">

        @csrf

        <div class="mb-5">
            <label class="input-label">المقرر</label>

            <select name="course_id" class="input-field" required>
                <option value="">اختر المقرر</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">
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

            <select name="department_id" class="input-field" required>
                <option value="">اختر القسم</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>

            @error('department_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">السمستر</label>

            <select name="semester_id" class="input-field" required>
                <option value="">اختر السمستر</option>
                @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}">
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
                <option value="">اختر المحاضر</option>
                @foreach($lecturers as $lecturer)
                    <option value="{{ $lecturer->id }}">
                        {{ $lecturer->name }}
                    </option>
                @endforeach
            </select>

            @error('lecturer_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn-gold">
            حفظ
        </button>

    </form>

</div>

@endsection
