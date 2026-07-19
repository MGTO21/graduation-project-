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

            <a href="{{ route('student.schedule') }}"
               class="side-link {{ request()->routeIs('student.schedule') ? 'side-link-active' : '' }}">
                الجدول الأسبوعي
            </a>

            <a href="{{ route('student.lectures.index') }}"
               class="side-link {{ request()->routeIs('student.lectures.index') ? 'side-link-active' : '' }}">
                المحاضرات المرفوعة
            </a>

            <a href="{{ route('student.lectures.past') }}"
               class="side-link {{ request()->routeIs('student.lectures.past') ? 'side-link-active' : '' }}">
                محاضرات سابقة
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
