@extends(auth()->user()->role === 'lecturer' ? 'layouts.lecturer' : 'layouts.student')

@section('content')

<div class="flex items-center justify-between mb-2">
    <h2 class="page-title">
        شات مقرر: {{ $courseOffering->course->name }}
    </h2>

    <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="btn-ghost">
        رجوع للوحة
    </a>
</div>

<p class="text-muted text-sm mb-8 pr-3">
    محاضر المقرر: {{ $courseOffering->lecturer->name }}
</p>

<div class="max-w-2xl">
    @include('chat._widget', ['courseOffering' => $courseOffering, 'messages' => $messages])
</div>

@endsection
