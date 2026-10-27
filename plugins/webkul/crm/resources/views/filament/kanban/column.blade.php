@php
    use Webkul\Crm\Enums\KanbanActivityState;

    $color = $board->resolveColumnColor($column);
    $wipLimit = $board->getWipLimit($column->value);
    $isOverWip = $board->isOverWipLimit($column);
    $summary = $board->resolveColumnSummary($column);
    $isCollapsible = $board->isCollapsible();
    $hasHeaderAction = $board->hasColumnHeaderAction();
    $hasEmptyState = $board->hasEmptyState() && $column->count === 0;
    $canEditColumn = method_exists($this, 'canEditKanbanColumn') && $this->canEditKanbanColumn($column->value);
    $canDeleteColumn = method_exists($this, 'canDeleteKanbanColumn') && $this->canDeleteKanbanColumn($column->value);

    $colorKey = in_array($color, ['primary', 'success', 'warning', 'danger', 'info'], true) ? $color : 'gray';
    $colorClasses = 'crm-kanban-column-'.$colorKey;
    $badgeColorClasses = 'crm-kanban-count-'.($isOverWip ? 'danger' : $colorKey);
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

    @php
        $progress = method_exists($this, 'kanbanColumnProgress') ? $this->kanbanColumnProgress($column) : [];
        $progressTotal = array_sum($progress);
        $activeProgress = $progressTotal ? ($this->kanbanActivityFilter[$column->value] ?? null) : null;
        $progressStates = collect(KanbanActivityState::cases())->mapWithKeys(fn (KanbanActivityState $state): array => [$state->value => [match ($state) {
            KanbanActivityState::PLANNED => 'bg-success-500',
            KanbanActivityState::TODAY   => 'bg-warning-400',
            KanbanActivityState::OVERDUE => 'bg-danger-500',
            KanbanActivityState::NONE    => 'bg-gray-300 dark:bg-gray-600',
        }, $state->getLabel()]])->all();
    @endphp

    @if($summary || $progressTotal)
        <div class="flex items-center gap-3 px-3 pb-2">
            @if($progressTotal)
                <div class="flex h-2.5 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-white/10" role="group" aria-label="{{ __('crm::filament/kanban.progress.label') }}">
                    @foreach($progressStates as $state => [$progressColor, $progressLabel])
                        @if(($progress[$state] ?? 0) > 0)
                            <button
                                type="button"
                                wire:click="toggleKanbanActivityFilter('{{ $column->value }}', '{{ $state }}')"
                                title="{{ $progressLabel }}: {{ $progress[$state] }}"
                                aria-label="{{ $progressLabel }}: {{ $progress[$state] }}"
                                aria-pressed="{{ $activeProgress === $state ? 'true' : 'false' }}"
                                style="width: {{ round($progress[$state] / $progressTotal * 100, 2) }}%;"
                                @class([
                                    'h-full transition hover:opacity-80',
                                    $progressColor,
                                    'opacity-30' => $activeProgress && $activeProgress !== $state,
                                ])
                            ></button>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($summary)
                <span class="shrink-0 text-xs font-semibold text-gray-700 dark:text-gray-300" title="{{ __('crm::filament/kanban.progress.total') }}">{{ $summary }}</span>
            @endif
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
            <div class="crm-kanban-empty">
                <x-filament::icon
                    :icon="$hasEmptyState && $board->getEmptyStateIcon() ? $board->getEmptyStateIcon() : 'heroicon-o-inbox'"
                    class="h-8 w-8 text-gray-300 dark:text-gray-600"
                />
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                    {{ $hasEmptyState ? $board->getEmptyStateHeading() : __('crm::filament/kanban.empty.heading') }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    {{ $hasEmptyState && $board->getEmptyStateDescription() ? $board->getEmptyStateDescription() : __('crm::filament/kanban.empty.description') }}
                </p>
            </div>
        @endforelse
    </div>
</div>
