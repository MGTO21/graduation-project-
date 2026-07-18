@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    لوحة التحكم
</h2>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-12">

    <div class="card">
        <h3 class="text-xs text-muted mb-3">الطلاب</h3>
        <p class="font-cairo font-black text-4xl text-gold">{{ $studentsCount }}</p>
    </div>

    <div class="card">
        <h3 class="text-xs text-muted mb-3">المحاضرون</h3>
        <p class="font-cairo font-black text-4xl text-gold">{{ $lecturersCount }}</p>
    </div>

    <div class="card">
        <h3 class="text-xs text-muted mb-3">الأقسام</h3>
        <p class="font-cairo font-black text-4xl text-gold">{{ $departmentsCount }}</p>
    </div>

    <div class="card">
        <h3 class="text-xs text-muted mb-3">المقررات</h3>
        <p class="font-cairo font-black text-4xl text-gold">{{ $coursesCount }}</p>
    </div>

    <div class="card">
        <h3 class="text-xs text-muted mb-3">المحاضرات</h3>
        <p class="font-cairo font-black text-4xl text-gold">{{ $lecturesCount }}</p>
    </div>

</div>

{{-- عداد البث المباشر بمفرده لأنه بيتغير لحظياً وأهم إنه يبان واضح لوحده --}}
<div class="card !border-danger/30 mb-12 max-w-xs">
    <h3 class="text-xs text-muted mb-3">محاضرات مباشرة الآن</h3>
    <p class="font-cairo font-black text-4xl {{ $liveLecturesCount > 0 ? 'text-danger' : 'text-gold' }}">
        {{ $liveLecturesCount }}
    </p>
</div>

{{-- توزيع الطلاب على الأقسام --}}
<h3 class="section-title mb-4">
    الطلاب حسب القسم
</h3>

<table class="table-n">
    <thead>
        <tr>
            <th>القسم</th>
            <th>عدد الطلاب</th>
        </tr>
    </thead>

    <tbody>
        @forelse($studentsByDepartment as $department)
            <tr>
                <td>{{ $department->name }}</td>
                <td>{{ $department->students_count }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="text-center !py-8 text-muted">
                    لا توجد أقسام مضافة بعد
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
