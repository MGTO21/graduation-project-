@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h2 class="page-title">
        طرح المقررات
    </h2>

    <a href="{{ route('admin.course-offerings.create') }}" class="btn-gold">
        إضافة طرح مقرر
    </a>
</div>

<table class="table-n">
    <thead>
        <tr>
            <th>المقرر</th>
            <th>القسم</th>
            <th>السمستر</th>
            <th>المحاضر</th>
            <th>الحالة</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
    @foreach($courseOffering as $courseOffering)
        <tr>
            <td>{{ $courseOffering->course->name }}</td>
            <td>{{ $courseOffering->department->name ?? 'كل الأقسام' }}</td>
            <td>{{ $courseOffering->semester->name }}</td>
            <td>{{ $courseOffering->lecturer->name }}</td>
            <td>
                @if($courseOffering->is_active)
                    <span class="text-success text-xs font-medium">نشط</span>
                @else
                    <span class="text-muted text-xs">غير نشط</span>
                @endif
            </td>
            <td>
                <div class="flex items-center gap-2">

                    <a href="{{ route('admin.course-offerings.edit', $courseOffering) }}" class="btn-edit">
                        تعديل
                    </a>

                    <form action="{{ route('admin.course-offerings.destroy', $courseOffering) }}"
                          method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف طرح المقرر؟')">

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
