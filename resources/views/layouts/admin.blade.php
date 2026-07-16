<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    @include('layouts.partials.head')
    <title>لوحة الإدارة - المنصة التعليمية</title>
</head>

<body>

@include('layouts.partials.topbar')

<div class="flex min-h-screen">

    <!-- القائمة الجانبية -->
    <aside class="w-60 bg-white border-l border-line shrink-0">
        <nav class="py-4">

            <a href="{{ route('admin.dashboard') }}"
               class="side-link {{ request()->routeIs('admin.dashboard') ? 'side-link-active' : '' }}">
                الرئيسية
            </a>

            <a href="{{ route('admin.departments.index') }}"
               class="side-link {{ request()->routeIs('admin.departments.*') ? 'side-link-active' : '' }}">
                الأقسام
            </a>

            <a href="{{ route('admin.semesters.index') }}"
               class="side-link {{ request()->routeIs('admin.semesters.*') ? 'side-link-active' : '' }}">
                السمسترات
            </a>

            <a href="{{ route('admin.courses.index') }}"
               class="side-link {{ request()->routeIs('admin.courses.*') ? 'side-link-active' : '' }}">
                المقررات
            </a>

            <a href="{{ route('admin.course-offerings.index') }}"
               class="side-link {{ request()->routeIs('admin.course-offerings.*') ? 'side-link-active' : '' }}">
                طرح المقررات
            </a>

            <a href="{{ route('admin.lecturers.index') }}"
               class="side-link {{ request()->routeIs('admin.lecturers.*') ? 'side-link-active' : '' }}">
                المحاضرون
            </a>

            <a href="{{ route('admin.students.index') }}"
               class="side-link {{ request()->routeIs('admin.students.*') ? 'side-link-active' : '' }}">
                الطلاب
            </a>

            <a href="{{ route('admin.lecture-schedules.index') }}"
               class="side-link {{ request()->routeIs('admin.lecture-schedules.*') ? 'side-link-active' : '' }}">
                الجداول الدراسية
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
