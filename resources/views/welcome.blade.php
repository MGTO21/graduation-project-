<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    @include('layouts.partials.head')
    <title>المنصة التعليمية - كلية علوم الحاسوب وتقانة المعلومات</title>
</head>

<body>

<div class="min-h-screen flex flex-col">

    {{-- شريط علوي بسيط --}}
    <header class="bg-white border-b border-line">
        <div class="max-w-5xl mx-auto flex items-center gap-3 px-6 py-3">
            <img src="{{ asset('images/brand/faculty-logo.png') }}"
                 alt="شعار الكلية"
                 class="w-10 h-10 rounded-full border-2 border-gold object-cover bg-white">
            <p class="font-cairo font-bold text-ink text-sm">
                كلية علوم الحاسوب وتقانة المعلومات
                <span class="block text-xs text-muted font-tajawal font-normal">جامعة النيلين</span>
            </p>
        </div>
    </header>

    {{-- الواجهة الرئيسية: صورة المبنى كخلفية --}}
    <main class="flex-1 relative flex items-center justify-center px-4 py-20"
          style="background-image: url('{{ asset('images/brand/campus-hero.jpg') }}'); background-size: cover; background-position: center;">

        {{-- طبقة تغطية داكنة لوضوح النص --}}
        <div class="absolute inset-0 bg-ink/75"></div>

        <div class="relative text-center max-w-2xl">

            {{-- الشعاران جنباً إلى جنب --}}
            <div class="flex items-center justify-center gap-6 mb-10">
                <img src="{{ asset('images/brand/university-logo.png') }}"
                     alt="شعار جامعة النيلين"
                     class="w-20 h-20 rounded-full border-2 border-gold object-cover bg-white p-1">
                <span class="w-px h-14 bg-gold/50"></span>
                <img src="{{ asset('images/brand/faculty-logo.png') }}"
                     alt="شعار الكلية"
                     class="w-20 h-20 rounded-full border-2 border-gold object-cover bg-white p-1">
            </div>

            <h1 class="font-cairo font-black text-4xl md:text-5xl text-white leading-tight mb-4">
                المنصة التعليمية
            </h1>

            <p class="text-lg text-cream mb-1">
                كلية علوم الحاسوب وتقانة المعلومات
            </p>

            <p class="text-cream/70 mb-10">
                جامعة النيلين
            </p>

            {{-- فاصل ذهبي رفيع --}}
            <div class="w-16 h-[2px] bg-gold mx-auto mb-10"></div>

            <a href="{{ route('login') }}" class="btn-gold !px-10 !py-3 !text-base">
                تسجيل الدخول
            </a>

        </div>
    </main>

    {{-- شريط صور من الحرم الجامعي --}}
    <section class="bg-white border-t border-line py-10">
        <div class="max-w-5xl mx-auto px-4">

            <h2 class="section-title mb-6">
                من الكلية
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="border border-line rounded-sm overflow-hidden">
                    <img src="{{ asset('images/brand/campus-building.jpg') }}"
                         alt="مبنى الكلية"
                         class="w-full h-56 object-cover">
                </div>
                <div class="border border-line rounded-sm overflow-hidden">
                    <img src="{{ asset('images/brand/campus-gate.jpg') }}"
                         alt="بوابة الكلية"
                         class="w-full h-56 object-cover">
                </div>
            </div>

        </div>
    </section>

    <footer class="border-t border-line py-4">
        <p class="text-center text-xs text-muted">
            كلية علوم الحاسوب وتقانة المعلومات — جامعة النيلين
        </p>
    </footer>

</div>

</body>
</html>
