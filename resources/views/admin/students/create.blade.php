@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    إضافة طالب
</h2>

<div class="card max-w-2xl">

    <form action="{{ route('admin.students.store') }}" method="POST">

        @csrf

        <div class="mb-5">
            <label class="input-label">الرقم الجامعي</label>
            <input type="text" name="university_id"
                   value="{{ old('university_id') }}"
                   class="input-field" required>

            @error('university_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">الاسم</label>
            <input type="text" name="name"
                   value="{{ old('name') }}"
                   class="input-field" required>

            @error('name')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">البريد الإلكتروني</label>
            <input type="email" name="email"
                   value="{{ old('email') }}"
                   class="input-field" required>

            @error('email')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">رقم الهاتف</label>
            <input type="text" name="phone"
                   value="{{ old('phone') }}"
                   class="input-field">

            @error('phone')
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
            <label class="input-label">كلمة المرور</label>
            <input type="password"
                   name="password"
                   class="input-field"
                   required>

            @error('password')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn-gold">
            حفظ
        </button>

    </form>

</div>

@endsection
