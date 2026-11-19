@php
    $size = $size ?? 'h-6 w-6';
    $text = $text ?? 'text-[10px]';
    $ring = $ring ?? '';
    $initials = \Illuminate\Support\Str::of($member->name)->explode(' ')->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('');
@endphp

@if ($member->avatar_url)
    <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" title="{{ $member->name }}" class="{{ $size }} shrink-0 rounded-full object-cover {{ $ring }}" />
@else
    <span class="projects-kanban-avatar flex {{ $size }} shrink-0 items-center justify-center rounded-full {{ $text }} font-semibold {{ $ring }}" title="{{ $member->name }}">
        {{ $initials }}
    </span>
@endif
