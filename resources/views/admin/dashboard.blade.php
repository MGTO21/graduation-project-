@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    لوحة التحكم
</h2>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">

    <div class="card relative overflow-hidden">
        <span class="absolute top-3 left-4 font-cairo font-black text-3xl text-line select-none">٠١</span>
        <h3 class="text-xs text-muted mb-3">الطلاب</h3>
        <p class="font-cairo font-black text-4xl text-gold">{{ $studentsCount }}</p>
    </div>

    <div class="card relative overflow-hidden">
        <span class="absolute top-3 left-4 font-cairo font-black text-3xl text-line select-none">٠٢</span>
        <h3 class="text-xs text-muted mb-3">المحاضرون</h3>
        <p class="font-cairo font-black text-4xl text-gold">{{ $lecturersCount }}</p>
    </div>

    <div class="card relative overflow-hidden">
        <span class="absolute top-3 left-4 font-cairo font-black text-3xl text-line select-none">٠٣</span>
        <h3 class="text-xs text-muted mb-3">الأقسام</h3>
        <p class="font-cairo font-black text-4xl text-gold">{{ $departmentsCount }}</p>
    </div>

    <div class="card relative overflow-hidden">
        <span class="absolute top-3 left-4 font-cairo font-black text-3xl text-line select-none">٠٤</span>
        <h3 class="text-xs text-muted mb-3">المقررات</h3>
        <p class="font-cairo font-black text-4xl text-gold">{{ $coursesCount }}</p>
    </div>

    <div class="card relative overflow-hidden">
        <span class="absolute top-3 left-4 font-cairo font-black text-3xl text-line select-none">٠٥</span>
        <h3 class="text-xs text-muted mb-3">المحاضرات</h3>
        <p class="font-cairo font-black text-4xl text-gold">{{ $lecturesCount }}</p>
    </div>

    <div class="card relative overflow-hidden">
        <span class="absolute top-3 left-4 font-cairo font-black text-3xl text-line select-none">٠٦</span>
        <h3 class="text-xs text-muted mb-3">محاضرات مباشرة الآن</h3>
        <p class="font-cairo font-black text-4xl {{ $liveLecturesCount > 0 ? 'text-danger' : 'text-gold' }}">
            {{ $liveLecturesCount }}
        </p>
    </div>

</div>

@endsection
