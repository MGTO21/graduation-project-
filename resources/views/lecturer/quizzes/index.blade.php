@extends('layouts.lecturer')

@section('content')

<div class="flex items-center justify-between mb-2">
    <h2 class="page-title">
        الاختبارات الفورية: {{ $courseOffering->course->name }}
    </h2>

    <div class="flex items-center gap-2">
        <a href="{{ route('lecturer.quizzes.create', $courseOffering) }}" class="btn-gold">
            سؤال جديد
        </a>

        <a href="{{ route('lecturer.dashboard') }}" class="btn-ghost">
            رجوع للوحة
        </a>
    </div>
</div>

<p class="text-muted text-sm mb-8 pr-3">
    سجل كل الأسئلة السابقة لهذا المقرر - محفوظة للمراجعة، ما بتتحذف
</p>

<table class="table-n">
    <thead>
        <tr>
            <th>السؤال</th>
            <th>الحالة</th>
            <th>عدد المجيبين</th>
            <th>الإجراءات</th>
        </tr>
    </thead>

    <tbody>
        @forelse($quizzes as $quiz)
            <tr>
                <td>{{ mb_strlen($quiz->question) > 60 ? mb_substr($quiz->question, 0, 60) . '...' : $quiz->question }}</td>
                <td>
                    @if($quiz->status === 'draft')
                        <span class="text-muted text-xs">لم يُطلق بعد</span>
                    @elseif($quiz->status === 'active')
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-danger">
                            <span class="h-1.5 w-1.5 rounded-full bg-danger"></span>
                            شغال الآن
                        </span>
                    @else
                        <span class="text-success text-xs font-medium">انتهى</span>
                    @endif
                </td>
                <td>{{ $quiz->answers_count }}</td>
                <td>
                    <a href="{{ route('lecturer.quizzes.show', $quiz) }}" class="btn-outline !px-3 !py-1 !text-xs">
                        عرض
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center !py-8 text-muted">
                    لا توجد أسئلة لهذا المقرر بعد
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
