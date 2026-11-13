@php
    $priority = (int) $action->getRecord()->priority;
@endphp

<div
    class="fi-ta-card-evaluation flex shrink-0 items-center gap-0.5 text-warning-500 dark:text-warning-400"
    title="{{ __('recruitments::filament/clusters/applications/resources/candidate.table.columns.evaluation') }}"
>
    @for ($i = 1; $i <= 3; $i++)
        <x-filament::icon
            :icon="$i <= $priority ? 'heroicon-s-star' : 'heroicon-o-star'"
            class="size-4"
        />
    @endfor
</div>
