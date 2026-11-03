@use('Filament\Support\Colors\Color')

@php
    $tags = $action->getRecord()->{$relationship};
@endphp

<div
    class="fi-ta-card-tags flex min-w-0 flex-1 items-center gap-1 overflow-hidden"
    title="{{ $tags->pluck('name')->join(', ') }}"
>
    @foreach ($tags as $tag)
        <x-filament::badge
            :color="Color::generateV3Palette($tag->color ?? '#808080')"
            class="shrink-0"
        >
            {{ $tag->name }}
        </x-filament::badge>
    @endforeach
</div>
