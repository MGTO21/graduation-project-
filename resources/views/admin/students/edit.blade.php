@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    تعديل بيانات الطالب
</h2>

<div class="card max-w-2xl">

    <form action="{{ route('admin.students.update', $student) }}" method="POST" autocomplete="off">

        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="input-label">الرقم الجامعي</label>
            <input type="text"
                   name="university_id"
                   value="{{ old('university_id', $student->university_id) }}"
                   class="input-field"
                   required>

            @error('university_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">الاسم</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $student->name) }}"
                   class="input-field"
                   required>

            @error('name')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">البريد الإلكتروني</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $student->email) }}"
                   class="input-field"
                   required>

            @error('email')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">رقم الهاتف</label>
            <input type="text"
                   name="phone"
                   value="{{ old('phone', $student->phone) }}"
                   class="input-field">

            @error('phone')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">القسم</label>

            <select name="department_id" class="input-field" required>

                @foreach($departments as $department)
                    <option value="{{ $department->id }}"
                        {{ $student->department_id == $department->id ? 'selected' : '' }}>
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

                @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}"
                        {{ $student->semester_id == $semester->id ? 'selected' : '' }}>
                        {{ $semester->name }}
                    </option>
                @endforeach

            </select>

            @error('semester_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn-gold">
            تحديث
        </button>

    </form>

</div>

@endsection
