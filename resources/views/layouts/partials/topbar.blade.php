@php
    $roleLabels = [
        'admin'    => 'مدير النظام',
        'lecturer' => 'محاضر',
        'student'  => 'طالب',
    ];
@endphp

<header class="bg-white border-b border-line">
    <div class="flex items-center justify-between px-6 py-3">

        {{-- الشعار واسم الكلية --}}
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/brand/faculty-logo.png') }}"
                 alt="شعار الكلية"
                 class="w-11 h-11 rounded-full border-2 border-gold object-cover bg-white">
            <div>
                <p class="font-cairo font-bold text-ink leading-tight">
                    كلية علوم الحاسوب وتقانة المعلومات
                </p>
                <p class="text-xs text-muted">جامعة النيلين</p>
            </div>
        </div>

        {{-- المستخدم وتسجيل الخروج --}}
        <div class="flex items-center gap-4">
            <div class="text-left">
                <p class="text-sm font-medium text-ink leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-xs text-muted">{{ $roleLabels[auth()->user()->role] ?? '' }}</p>
            </div>

            <span class="h-8 w-px bg-line"></span>

            <form action="{{ route('logout') }}" method="POST"
                  onsubmit="return confirm('هل أنت متأكد من تسجيل الخروج؟')">
                @csrf
                <button type="submit" class="btn-ghost !px-4 !py-1.5 text-xs">
                    تسجيل الخروج
                </button>
            </form>
        </div>

    </div>
</header>
