@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    تعديل المقرر
</h2>

<div class="card max-w-2xl">

    <form action="{{ route('admin.courses.update', $course) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="input-label">رمز المقرر</label>

            <input
                type="text"
                name="code"
                value="{{ old('code', $course->code) }}"
                class="input-field"
                required>

            @error('code')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">اسم المقرر</label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $course->name) }}"
                class="input-field"
                required>

            @error('name')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">عدد الساعات</label>

            <input
                type="number"
                name="credit_hours"
                value="{{ old('credit_hours', $course->credit_hours) }}"
                min="1"
                max="6"
                class="input-field"
                required>

            @error('credit_hours')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">وصف المقرر</label>

            <textarea
                name="description"
                rows="4"
                class="input-field">{{ old('description', $course->description) }}</textarea>

            @error('description')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn-gold">
            تحديث
        </button>

    </form>

</div>

@endsection
