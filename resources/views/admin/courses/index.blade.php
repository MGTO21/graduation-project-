@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h2 class="page-title">
        إدارة المقررات
    </h2>

    <a href="{{ route('admin.courses.create') }}" class="btn-gold">
        إضافة مقرر
    </a>
</div>

<table class="table-n">
    <thead>
        <tr>
            <th>الكود</th>
            <th>اسم المقرر</th>
            <th>الساعات</th>
            <th>الحالة</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
    @foreach($courses as $course)
        <tr>
            <td>{{ $course->code }}</td>
            <td>{{ $course->name }}</td>
            <td>{{ $course->credit_hours }}</td>
            <td>
                @if($course->is_active)
                    <span class="text-success text-xs font-medium">نشط</span>
                @else
                    <span class="text-muted text-xs">غير نشط</span>
                @endif
            </td>

            <td>
                <div class="flex items-center gap-2">

                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn-edit">
                        تعديل
                    </a>

                    <form action="{{ route('admin.courses.destroy', $course) }}"
                          method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المقرر؟')">

                        @csrf
                        @method('DELETE')

                        <button class="btn-del">
                            حذف
                        </button>

                    </form>

                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection
