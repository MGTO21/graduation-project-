<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    @include('layouts.partials.head')
    <title>تسجيل الدخول - المنصة التعليمية</title>
</head>

<body>

<div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">

    {{-- شعارا الجامعة والكلية --}}
    <a href="/" class="flex items-center gap-4 mb-6">
        <img src="{{ asset('images/brand/university-logo.png') }}"
             alt="شعار جامعة النيلين"
             class="w-16 h-16 rounded-full border-2 border-gold object-cover bg-white p-1">
        <img src="{{ asset('images/brand/faculty-logo.png') }}"
             alt="شعار الكلية"
             class="w-16 h-16 rounded-full border-2 border-gold object-cover bg-white p-1">
    </a>

    <p class="font-cairo font-bold text-ink mb-1">
        كلية علوم الحاسوب وتقانة المعلومات
    </p>
    <p class="text-xs text-muted mb-8">جامعة النيلين</p>

    {{-- كرت الدخول --}}
    <div class="w-full sm:max-w-md card !p-8">
        {{ $slot }}
    </div>

</div>

</body>
</html>
