@use('Filament\Support\Colors\Color')

@php
    $tags = $action->getRecord()->{$relationship};
@endphp

<div
    class="min-w-0 flex-1 truncate"
    title="{{ $tags->pluck('name')->join(', ') }}"
>
    @foreach ($tags as $tag)
        <x-filament::badge
            :color="Color::generateV3Palette($tag->color ?? '#808080')"
            class="align-middle"
        >
            {{ $tag->name }}
        </x-filament::badge>
    @endforeach
</div>
