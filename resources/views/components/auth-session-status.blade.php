@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-sm text-success font-medium']) }}>
        {{ $status }}
    </div>
@endif
