@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h2 class="page-title">
        إدارة الطلاب
    </h2>

    <a href="{{ route('admin.students.create') }}" class="btn-gold">
        إضافة طالب
    </a>
</div>

<table class="table-n">
    <thead>
        <tr>
            <th>الرقم الجامعي</th>
            <th>الاسم</th>
            <th>البريد الإلكتروني</th>
            <th>القسم</th>
            <th>السمستر</th>
            <th>الحالة</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
    @foreach($students as $student)
        <tr>
            <td>{{ $student->university_id }}</td>
            <td>{{ $student->name }}</td>
            <td>{{ $student->email }}</td>
            <td>
                {{ $student->department?->name ?? '-' }}
            </td>
            <td>{{ $student->semester?->name ?? '-' }}</td>
            <td>
                @if($student->is_active)
                    <span class="text-success text-xs font-medium">نشط</span>
                @else
                    <span class="text-muted text-xs">غير نشط</span>
                @endif
            </td>
            <td>
                <div class="flex items-center gap-2">

                    <a href="{{ route('admin.students.edit', $student) }}" class="btn-edit">
                        تعديل
                    </a>

                    <form action="{{ route('admin.students.destroy', $student) }}"
                          method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟')">

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
