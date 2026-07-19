@php
    $layout = match(auth()->user()->role) {
        'admin'    => 'layouts.admin',
        'lecturer' => 'layouts.lecturer',
        'student'  => 'layouts.student',
    };

    $roleLabels = [
        'admin'    => 'مدير النظام',
        'lecturer' => 'محاضر',
        'student'  => 'طالب',
    ];
@endphp

@extends($layout)

@section('content')

<h2 class="page-title mb-8">
    الملف الشخصي
</h2>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- الصورة الشخصية --}}
    <div class="card text-center">

        @if($user->profile_image)
            <img src="{{ asset('storage/' . $user->profile_image) }}"
                 alt="الصورة الشخصية"
                 class="w-28 h-28 rounded-full object-cover border-2 border-gold mx-auto mb-4">
        @else
            <div class="logo-circle w-28 h-28 text-3xl mx-auto mb-4">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
        @endif

        <p class="font-cairo font-bold text-ink">{{ $user->name }}</p>
        <p class="text-xs text-muted mb-6">{{ $roleLabels[$user->role] ?? '' }}</p>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- بنبعت الاسم والبريد الحاليين مع الفورم عشان التحديث ما يفضّي حقولهم لو الفورم الرئيسي
                 ما اتبعتش مع نفس الطلب (الصورة ليها فورم منفصل عشان input file) --}}
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">
            <input type="hidden" name="phone" value="{{ $user->phone }}">

            <label class="btn-outline !text-xs cursor-pointer inline-block">
                تغيير الصورة
                <input type="file" name="profile_image" accept="image/*" class="hidden" onchange="this.form.submit()">
            </label>

            @error('profile_image')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </form>

    </div>

    {{-- البيانات الأساسية --}}
    <div class="card lg:col-span-2">

        <h3 class="section-title mb-5">
            البيانات الأساسية
        </h3>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

                {{-- الرقم الجامعي للعرض فقط - هوية الدخول، الأدمن بس اللي يقدر يغيرها --}}
                <div>
                    <label class="input-label">الرقم الجامعي</label>
                    <input type="text" value="{{ $user->university_id }}" class="input-field bg-sand text-muted" disabled>
                </div>

                <div>
                    <label class="input-label">الاسم</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field" required>
                    @error('name')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="input-label">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required>
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="input-label">رقم الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input-field">
                    @error('phone')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                {{-- القسم والسمستر للعرض فقط - الأدمن بس اللي يقدر يغيرهم من لوحته --}}
                @if($user->department)
                    <div>
                        <label class="input-label">القسم</label>
                        <input type="text" value="{{ $user->department->name }}" class="input-field bg-sand text-muted" disabled>
                    </div>
                @endif

                @if($user->semester)
                    <div>
                        <label class="input-label">السمستر</label>
                        <input type="text" value="{{ $user->semester->name }}" class="input-field bg-sand text-muted" disabled>
                    </div>
                @endif

            </div>

            <button class="btn-gold">
                حفظ التعديلات
            </button>

            @if(session('status') === 'profile-updated')
                <span class="text-success text-sm mr-3">تم الحفظ بنجاح.</span>
            @endif

        </form>

    </div>

</div>

{{-- تغيير كلمة المرور --}}
<div class="card max-w-2xl mt-6">

    <h3 class="section-title mb-5">
        تغيير كلمة المرور
    </h3>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="input-label">كلمة المرور الحالية</label>
            <input type="password" name="current_password" class="input-field" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">كلمة المرور الجديدة</label>
            <input type="password" name="password" class="input-field" autocomplete="new-password">
            @error('password', 'updatePassword')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5">
            <label class="input-label">تأكيد كلمة المرور الجديدة</label>
            <input type="password" name="password_confirmation" class="input-field" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn-gold">
            تحديث كلمة المرور
        </button>

        @if(session('status') === 'password-updated')
            <span class="text-success text-sm mr-3">تم تحديث كلمة المرور.</span>
        @endif

    </form>

</div>

@endsection
