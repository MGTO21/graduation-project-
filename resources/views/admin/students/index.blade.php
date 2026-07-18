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

{{-- فلترة القائمة حسب القسم والسمستر - أي تغيير في الاختيار يعيد تحميل الصفحة بنفس الفلتر --}}
<form method="GET" class="flex flex-wrap items-end gap-4 mb-6">

    <div>
        <label class="input-label">القسم</label>
        <select name="department_id" class="input-field" onchange="this.form.submit()">
            <option value="">كل الأقسام</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="input-label">السمستر</label>
        <select name="semester_id" class="input-field" onchange="this.form.submit()">
            <option value="">كل السمسترات</option>
            @foreach($semesters as $semester)
                <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                    {{ $semester->name }}
                </option>
            @endforeach
        </select>
    </div>

    @if(request('department_id') || request('semester_id'))
        <a href="{{ route('admin.students.index') }}" class="btn-ghost !py-2.5">
            إلغاء الفلترة
        </a>
    @endif

</form>

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
