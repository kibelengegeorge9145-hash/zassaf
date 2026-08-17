@props(['user', 'class' => ''])

@php
    $user = $user ?? auth()->user();
    $size = ($class ?? '');
@endphp

<span class="avatar {{ $class }}" aria-hidden="true">
    @if ($user && $user->avatar_url)
        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
    @else
        <span class="avatar-initials">{{ $user?->initials ?? 'ZA' }}</span>
    @endif
</span>
