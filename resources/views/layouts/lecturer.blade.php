<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    @include('layouts.partials.head')
    <title>لوحة المحاضر - المنصة التعليمية</title>
</head>

<body>

@include('layouts.partials.topbar')

<div class="flex min-h-screen">

    <!-- القائمة الجانبية -->
    <aside class="w-60 bg-white border-l border-line shrink-0">
        <nav class="py-4">

            <a href="{{ route('lecturer.dashboard') }}"
               class="side-link {{ request()->routeIs('lecturer.dashboard') ? 'side-link-active' : '' }}">
                الرئيسية
            </a>

            <a href="{{ route('lecturer.schedule') }}"
               class="side-link {{ request()->routeIs('lecturer.schedule') ? 'side-link-active' : '' }}">
                الجدول الأسبوعي
            </a>

            <a href="{{ route('profile.edit') }}"
               class="side-link {{ request()->routeIs('profile.edit') ? 'side-link-active' : '' }}">
                الملف الشخصي
            </a>

        </nav>
    </aside>

    <!-- المحتوى -->
    <main class="flex-1 p-8">

        @include('layouts.partials.flash')

        @yield('content')

    </main>

</div>

</body>
</html>
