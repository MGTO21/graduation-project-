@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    إضافة قسم جديد
</h2>

<div class="card max-w-2xl">

    <form action="{{ route('admin.departments.store') }}" method="POST">

        @csrf

        <div class="mb-5">
            <label class="input-label">اسم القسم</label>

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="input-field"
                required>

            @error('name')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">رمز القسم</label>

            <input
                type="text"
                name="code"
                value="{{ old('code') }}"
                class="input-field"
                placeholder="مثال: IT"
                required>

            @error('code')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn-gold">
            حفظ
        </button>

    </form>

</div>

@endsection
