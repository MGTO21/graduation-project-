@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h2 class="page-title">
        إدارة المحاضرين
    </h2>

    <a href="{{ route('admin.lecturers.create') }}" class="btn-gold">
        إضافة محاضر
    </a>
</div>

<table class="table-n">
    <thead>
        <tr>
            <th>الرقم الجامعي</th>
            <th>الاسم</th>
            <th>البريد الإلكتروني</th>
            <th>القسم</th>
            <th>الحالة</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
    @foreach($lecturers as $lecturer)
        <tr>
            <td>{{ $lecturer->university_id }}</td>
            <td>{{ $lecturer->name }}</td>
            <td>{{ $lecturer->email }}</td>
            <td>
                {{ $lecturer->department?->name ?? '-' }}
            </td>
            <td>
                @if($lecturer->is_active)
                    <span class="text-success text-xs font-medium">نشط</span>
                @else
                    <span class="text-muted text-xs">غير نشط</span>
                @endif
            </td>
            <td>
                <div class="flex items-center gap-2">

                    <a href="{{ route('admin.lecturers.edit', $lecturer) }}" class="btn-edit">
                        تعديل
                    </a>

                    <form action="{{ route('admin.lecturers.destroy', $lecturer) }}"
                          method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المحاضر؟')">

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
