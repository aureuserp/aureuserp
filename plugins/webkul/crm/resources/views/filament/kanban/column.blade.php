@php
    $color = $board->resolveColumnColor($column);
    $wipLimit = $board->getWipLimit($column->value);
    $isOverWip = $board->isOverWipLimit($column);
    $summary = $board->resolveColumnSummary($column);
    $isCollapsible = $board->isCollapsible();
    $hasHeaderAction = $board->hasColumnHeaderAction();
    $hasEmptyState = $board->hasEmptyState() && $column->count === 0;
    $canEditColumn = method_exists($this, 'canEditKanbanColumn') && $this->canEditKanbanColumn($column->value);
    $canDeleteColumn = method_exists($this, 'canDeleteKanbanColumn') && $this->canDeleteKanbanColumn($column->value);

    $colorClasses = match($color) {
        'primary' => 'bg-primary-50 dark:bg-primary-400/10 border-primary-200 dark:border-primary-400/20',
        'success' => 'bg-success-50 dark:bg-success-400/10 border-success-200 dark:border-success-400/20',
        'warning' => 'bg-warning-50 dark:bg-warning-400/10 border-warning-200 dark:border-warning-400/20',
        'danger' => 'bg-danger-50 dark:bg-danger-400/10 border-danger-200 dark:border-danger-400/20',
        'info' => 'bg-info-50 dark:bg-info-400/10 border-info-200 dark:border-info-400/20',
        default => 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10',
    };

    $badgeColorClasses = $isOverWip
        ? 'bg-danger-100 text-danger-700 dark:bg-danger-400/20 dark:text-danger-400'
        : match($color) {
            'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary-400/20 dark:text-primary-400',
            'success' => 'bg-success-100 text-success-700 dark:bg-success-400/20 dark:text-success-400',
            'warning' => 'bg-warning-100 text-warning-700 dark:bg-warning-400/20 dark:text-warning-400',
            'danger' => 'bg-danger-100 text-danger-700 dark:bg-danger-400/20 dark:text-danger-400',
            'info' => 'bg-info-100 text-info-700 dark:bg-info-400/20 dark:text-info-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-400',
        };
@endphp

<div
    @class([
        'fi-kanban-column flex-shrink-0 rounded-xl border',
        $colorClasses,
        'ring-2 ring-danger-500/50' => $isOverWip,
    ])
    style="width: {{ $board->getColumnWidth() }};"
    @if($isCollapsible)
        x-data="{ collapsed: localStorage.getItem('kanban-col-{{ $column->value }}') === '1' }"
    @endif
    role="group"
    aria-label="{{ $column->label }}"
>
    {{-- Column header --}}
    <div class="flex items-center justify-between gap-2 p-3">
        <div class="flex items-center gap-2 min-w-0">
            @if($isCollapsible)
                <button
                    type="button"
                    x-on:click="collapsed = !collapsed; localStorage.setItem('kanban-col-{{ $column->value }}', collapsed ? '1' : '0')"
                    class="shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                    :aria-expanded="(!collapsed).toString()"
                    aria-label="Toggle {{ $column->label }}"
                >
                    <x-filament::icon
                        icon="heroicon-m-chevron-down"
                        class="h-4 w-4 transition-transform duration-200"
                        x-bind:class="collapsed ? '-rotate-90' : ''"
                    />
                </button>
            @endif

            @if($column->icon)
                <x-filament::icon
                    :icon="$column->icon"
                    class="h-5 w-5 text-gray-500 dark:text-gray-400 shrink-0"
                />
            @endif
            <h3 class="text-sm font-semibold text-gray-950 dark:text-white truncate">
                {{ $column->label }}
            </h3>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <span
                class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeColorClasses }}"
                @if($isOverWip) title="Over WIP limit ({{ $wipLimit }})" @endif
            >
                {{ $column->count }}@if($wipLimit) / {{ $wipLimit }}@endif
            </span>

            @if($canEditColumn || $canDeleteColumn)
                <x-filament::dropdown placement="bottom-end" teleport>
                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="rounded-md p-0.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200/50 dark:text-gray-500 dark:hover:text-gray-300 dark:hover:bg-white/10 transition"
                            aria-label="{{ __('crm::filament/kanban.column-actions.settings', ['name' => $column->label]) }}"
                            title="{{ __('crm::filament/kanban.column-actions.settings', ['name' => $column->label]) }}"
                        >
                            <x-filament::icon icon="heroicon-m-cog-6-tooth" class="h-4 w-4" />
                        </button>
                    </x-slot>

                    <x-filament::dropdown.list>
                        @if($canEditColumn)
                            <x-filament::dropdown.list.item
                                icon="heroicon-m-pencil-square"
                                wire:click="mountAction('editKanbanColumn', { column: '{{ $column->value }}' })"
                            >
                                {{ __('crm::filament/kanban.column-actions.edit') }}
                            </x-filament::dropdown.list.item>
                        @endif

                        @if($canDeleteColumn)
                            <x-filament::dropdown.list.item
                                icon="heroicon-m-trash"
                                color="danger"
                                wire:click="mountAction('deleteKanbanColumn', { column: '{{ $column->value }}' })"
                            >
                                {{ __('crm::filament/kanban.column-actions.delete') }}
                            </x-filament::dropdown.list.item>
                        @endif
                    </x-filament::dropdown.list>
                </x-filament::dropdown>
            @endif

            @if($hasHeaderAction)
                <button
                    type="button"
                    wire:click="mountAction('kanbanColumn', { column: '{{ $column->value }}' })"
                    class="rounded-md p-0.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200/50 dark:text-gray-500 dark:hover:text-gray-300 dark:hover:bg-white/10 transition"
                    aria-label="Add to {{ $column->label }}"
                >
                    <x-filament::icon icon="heroicon-m-plus" class="h-4 w-4" />
                </button>
            @endif
        </div>
    </div>

    {{-- Column summary --}}
    @if($summary)
        <div class="px-3 pb-1 text-xs text-gray-500 dark:text-gray-400">
            {{ $summary }}
        </div>
    @endif

    {{-- Card container (SortableJS target) --}}
    <div
        data-kanban-column
        data-column-value="{{ $column->value }}"
        class="fi-kanban-cards space-y-2 p-2 min-h-[60px]"
        @if($isCollapsible) x-show="!collapsed" @endif
        role="list"
    >
        @forelse($column->records as $record)
            @include($board->getCardView(), [
                'record' => $record,
                'board' => $board,
                'column' => $column,
            ])
        @empty
            @if($hasEmptyState)
                <div class="flex flex-col items-center justify-center py-6 text-center">
                    @if($board->getEmptyStateIcon())
                        <x-filament::icon
                            :icon="$board->getEmptyStateIcon()"
                            class="h-8 w-8 text-gray-300 dark:text-gray-600 mb-2"
                        />
                    @endif
                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500">
                        {{ $board->getEmptyStateHeading() }}
                    </p>
                    @if($board->getEmptyStateDescription())
                        <p class="text-xs text-gray-400 dark:text-gray-600 mt-0.5">
                            {{ $board->getEmptyStateDescription() }}
                        </p>
                    @endif
                </div>
            @endif
        @endforelse
    </div>
</div>
