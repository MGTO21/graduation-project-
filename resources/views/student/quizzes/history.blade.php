@extends('layouts.student')

@section('content')

<h2 class="page-title mb-8">
    اختباراتي السابقة
</h2>

<table class="table-n">
    <thead>
        <tr>
            <th>المقرر</th>
            <th>السؤال</th>
            <th>إجابتي</th>
            <th>النتيجة</th>
        </tr>
    </thead>

    <tbody>
        @forelse($answers as $answer)
            <tr>
                <td>{{ $answer->quiz->courseOffering->course->name }}</td>
                <td>{{ mb_strlen($answer->quiz->question) > 60 ? mb_substr($answer->quiz->question, 0, 60) . '...' : $answer->quiz->question }}</td>
                <td>{{ strtoupper($answer->selected_option) }}</td>
                <td>
                    @if($answer->is_correct)
                        <span class="text-success text-xs font-medium">إجابة صحيحة</span>
                    @else
                        <span class="text-danger text-xs font-medium">إجابة خاطئة</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center !py-8 text-muted">
                    لم تُجب على أي اختبار فوري بعد
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
