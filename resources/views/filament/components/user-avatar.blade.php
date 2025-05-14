@props(['name' => ''])

@php
    $nameString = is_callable($name) ? $name() : $name;
    $initials = collect(explode(' ', trim($nameString)))
        ->filter()
        ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
        ->join('');
@endphp

<div class="flex items-center space-x-2">
    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-primary-500 text-white text-sm font-bold">
        {{ $initials ?: '?' }}
    </div>
    <span>{{ $nameString }}</span>
</div>
