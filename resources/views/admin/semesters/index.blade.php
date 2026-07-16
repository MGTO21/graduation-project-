@extends('layouts.admin')

@section('content')

<h2 class="page-title mb-8">
    السمسترات الدراسية
</h2>

<table class="table-n">
    <thead>
        <tr>
            <th>الرقم</th>
            <th>السنة الدراسية</th>
            <th>الاسم</th>
            <th>الحالة</th>
        </tr>
    </thead>

    <tbody>
    @foreach($semesters as $semester)
        <tr>
            <td>{{ $semester->number }}</td>
            <td>{{ $semester->academic_year }}</td>
            <td>{{ $semester->name }}</td>
            <td>
                @if($semester->is_active)
                    <span class="text-success text-xs font-medium">نشط</span>
                @else
                    <span class="text-muted text-xs">غير نشط</span>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection
