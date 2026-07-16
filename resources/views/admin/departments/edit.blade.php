@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    تعديل القسم
</h2>

<div class="card max-w-2xl">

    <form action="{{ route('admin.departments.update', $department) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="input-label">رمز القسم</label>

            <input
                type="text"
                name="code"
                value="{{ old('code', $department->code) }}"
                class="input-field"
                required>

            @error('code')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">اسم القسم</label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $department->name) }}"
                class="input-field"
                required>

            @error('name')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn-gold">
            حفظ التعديلات
        </button>

    </form>

</div>

@endsection
