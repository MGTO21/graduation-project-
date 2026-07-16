<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    @include('layouts.partials.head')
    <title>لوحة الطالب - المنصة التعليمية</title>
</head>

<body>

@include('layouts.partials.topbar')

<div class="flex min-h-screen">

    <!-- القائمة الجانبية -->
    <aside class="w-60 bg-white border-l border-line shrink-0">
        <nav class="py-4">

            <a href="{{ route('student.dashboard') }}"
               class="side-link {{ request()->routeIs('student.dashboard') ? 'side-link-active' : '' }}">
                الرئيسية
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
