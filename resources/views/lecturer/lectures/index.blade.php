@extends('layouts.lecturer')

@section('content')

<div class="flex items-center justify-between mb-2">
    <h2 class="page-title">
        محاضرات مقرر: {{ $courseOffering->course->name }}
    </h2>

    <div class="flex items-center gap-2">
        <a href="{{ route('lecturer.lectures.create', $courseOffering) }}" class="btn-gold">
            إضافة محاضرة
        </a>

        <a href="{{ route('lecturer.dashboard') }}" class="btn-ghost">
            رجوع للوحة
        </a>
    </div>
</div>

<p class="text-muted text-sm mb-8 pr-3">
    {{ $courseOffering->department->name }} — {{ $courseOffering->semester->name }}
</p>

<div class="space-y-6">

    @forelse($lectures as $lecture)

        <div class="card">

            <div class="flex justify-between items-center mb-4 pb-3 border-b border-line">

                <div class="text-sm">
                    <span class="font-cairo font-bold text-ink">تاريخ المحاضرة:</span>
                    <span class="text-body">{{ $lecture->lecture_date }}</span>

                    <span class="text-muted mr-3 text-xs">
                        ({{ $days[$lecture->lectureSchedule->day] }}
                        {{ substr($lecture->lectureSchedule->start_time, 0, 5) }}
                        -
                        {{ substr($lecture->lectureSchedule->end_time, 0, 5) }})
                    </span>
                </div>

                <form action="{{ route('lecturer.lectures.destroy', $lecture) }}"
                    method="POST"
                    class="inline">

                    @csrf
                    @method('DELETE')

                    <button
                        onclick="return confirm('هل أنت متأكد من حذف المحاضرة وكل ملفاتها؟')"
                        class="btn-del">
                        حذف المحاضرة
                    </button>

                </form>

            </div>

            {{-- ملفات المحاضرة --}}
            @if($lecture->files->count())

                <table class="table-n">
                    <thead>
                        <tr>
                            <th>اسم الملف</th>
                            <th>النوع</th>
                            <th>الحجم</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($lecture->files as $file)
                            <tr>
                                <td>{{ $file->original_name }}</td>
                                <td>{{ $file->file_type }}</td>
                                <td>{{ round($file->file_size / 1048576, 2) }} MB</td>
                                <td>
                                    <div class="flex items-center gap-2">

                                        <a href="{{ asset('storage/' . $file->file_path) }}"
                                            target="_blank"
                                            class="btn-edit">
                                            عرض
                                        </a>

                                        <form action="{{ route('lecturer.lecture-files.destroy', $file) }}"
                                            method="POST"
                                            class="inline">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                onclick="return confirm('هل أنت متأكد من حذف الملف؟')"
                                                class="btn-del">
                                                حذف
                                            </button>

                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @else
                <p class="text-muted text-sm">
                    لا توجد ملفات لهذه المحاضرة
                </p>
            @endif

        </div>

    @empty
        <div class="card text-center">
            <p class="text-muted">
                لا توجد محاضرات لهذا المقرر بعد
            </p>
        </div>
    @endforelse

</div>

@endsection
