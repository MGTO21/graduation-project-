@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h2 class="page-title">
        إدارة الأقسام
    </h2>

    <a href="{{ route('admin.departments.create') }}" class="btn-gold">
        إضافة قسم
    </a>
</div>

<table class="table-n">
    <thead>
        <tr>
            <th>رقم القسم</th>
            <th>رمز القسم</th>
            <th>اسم القسم</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
        @forelse($departments as $department)
            <tr>
                <td>{{ $department->id }}</td>
                <td>{{ $department->code }}</td>
                <td>{{ $department->name }}</td>

                <td>
                    <div class="flex items-center gap-2">

                        <a href="{{ route('admin.departments.edit', $department) }}" class="btn-edit">
                            تعديل
                        </a>

                        <form action="{{ route('admin.departments.destroy', $department) }}"
                            method="POST"
                            class="inline">

                            @csrf
                            @method('DELETE')

                            <button
                                onclick="return confirm('هل أنت متأكد من حذف القسم؟')"
                                class="btn-del">
                                حذف
                            </button>

                        </form>

                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center !py-8 text-muted">
                    لا توجد أقسام
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
