@extends('layouts.lecturer')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h2 class="page-title">
        سؤال فوري جديد: {{ $courseOffering->course->name }}
    </h2>

    <a href="{{ route('lecturer.quizzes.index', $courseOffering) }}" class="btn-ghost">
        رجوع
    </a>
</div>

<div class="card max-w-2xl">

    <form action="{{ route('lecturer.quizzes.store', $courseOffering) }}" method="POST">

        @csrf

        <div class="mb-5">
            <label class="input-label">نص السؤال</label>

            <textarea name="question" rows="3" class="input-field" required>{{ old('question') }}</textarea>

            @error('question')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

            <div>
                <label class="input-label">الخيار أ</label>
                <input type="text" name="option_a" value="{{ old('option_a') }}" class="input-field" required>
                @error('option_a')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="input-label">الخيار ب</label>
                <input type="text" name="option_b" value="{{ old('option_b') }}" class="input-field" required>
                @error('option_b')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="input-label">الخيار ج</label>
                <input type="text" name="option_c" value="{{ old('option_c') }}" class="input-field" required>
                @error('option_c')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="input-label">الخيار د</label>
                <input type="text" name="option_d" value="{{ old('option_d') }}" class="input-field" required>
                @error('option_d')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

            <div>
                <label class="input-label">الخيار الصحيح</label>

                <select name="correct_option" class="input-field" required>
                    <option value="">-- اختر --</option>
                    <option value="a" {{ old('correct_option') === 'a' ? 'selected' : '' }}>أ</option>
                    <option value="b" {{ old('correct_option') === 'b' ? 'selected' : '' }}>ب</option>
                    <option value="c" {{ old('correct_option') === 'c' ? 'selected' : '' }}>ج</option>
                    <option value="d" {{ old('correct_option') === 'd' ? 'selected' : '' }}>د</option>
                </select>

                @error('correct_option')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="input-label">مدة الإجابة (بالثواني)</label>
                <input type="number" name="duration_seconds" value="{{ old('duration_seconds', 60) }}" min="10" max="600" class="input-field" required>
                @error('duration_seconds')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <p class="text-xs text-muted mb-5">
            السؤال بيتحفظ الآن كمسودة فقط - الطلاب ما يشوفوه إلا لما تضغط "إطلاق السؤال" من الصفحة اللي بعدها.
        </p>

        <button class="btn-gold">
            حفظ ومتابعة
        </button>

    </form>

</div>

@endsection
