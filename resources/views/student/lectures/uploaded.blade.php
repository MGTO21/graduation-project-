@extends('layouts.student')

@section('content')

<h2 class="page-title mb-8">
    المحاضرات المرفوعة
</h2>

<table class="table-n">
    <thead>
        <tr>
            <th>المقرر</th>
            <th>المحاضر</th>
            <th>تاريخ المحاضرة</th>
            <th>عدد الملفات</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
        @forelse($lectures as $lecture)
            <tr>
                <td>{{ $lecture->lectureSchedule->courseOffering->course->name }}</td>
                <td>{{ $lecture->lectureSchedule->courseOffering->lecturer->name }}</td>
                <td>{{ $lecture->lecture_date }}</td>
                <td>{{ $lecture->files->count() }}</td>
                <td>
                    <a href="{{ route('student.lectures.show', $lecture) }}" class="btn-outline !px-3 !py-1 !text-xs">
                        مشاهدة المحاضرة
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center !py-8 text-muted">
                    لا توجد محاضرات مرفوعة بعد
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
