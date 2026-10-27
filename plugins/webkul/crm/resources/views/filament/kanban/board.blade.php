@php
    $board = $this->getKanbanBoard();
    $columns = $this->getKanbanColumns();
    $hasFilters = $board->hasFilters();
    $hasSearch = $board->isSearchable();
    $hasLoading = $board->hasLoading();
    $defaultKanbanFilters = method_exists($this, 'defaultKanbanFilters') ? $this->defaultKanbanFilters() : [];
    $activeFilterCount = collect($this->kanbanFilters)->filter(fn ($v, $key) => filled($v) && $v !== ($defaultKanbanFilters[$key] ?? null))->count();
@endphp

<div
    x-load
    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-kanban', 'wezlo/filament-kanban') }}"
    x-data="kanbanBoard({
        hasOrderColumn: @js(filled($board->getOrderColumn())),
        hasCardAction: @js($board->hasCardClickAction()),
        collapsible: @js($board->isCollapsible()),
        dragConstraints: @js($board->getDragConstraints()),
    })"
    x-init="window.crmScheduleKanbanFit ? window.crmScheduleKanbanFit() : $nextTick(() => window.crmFitKanbanBoard?.($el))"
    class="fi-kanban-board crm-kanban-board relative"
>
    @if($hasFilters || $hasSearch)
        <div class="crm-kanban-toolbar">
            @if($hasSearch)
                <div class="crm-kanban-search">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                            <x-filament::icon
                                icon="heroicon-m-magnifying-glass"
                                class="h-5 w-5 text-gray-400 dark:text-gray-500"
                            />
                        </div>
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="kanbanSearch"
                            placeholder="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.search') }}"
                            aria-label="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.search') }}"
                            class="fi-input block w-full rounded-lg border-none bg-white py-2 ps-10 pe-3 text-sm shadow-sm ring-1 ring-gray-950/10 transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20 dark:placeholder:text-gray-500"
                        />
                    </div>
                </div>
            @endif

            @if($hasFilters)
                <div x-data="{ open: false }" class="crm-kanban-filters">
                    <button
                        type="button"
                        x-on:click="open = !open"
                        :aria-expanded="open.toString()"
                        aria-controls="kanban-filters-panel"
                        aria-label="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.toggle') }}"
                        @class([
                            'relative flex items-center justify-center rounded-lg p-2 text-sm shadow-sm ring-1 transition duration-75',
                            'bg-white ring-gray-950/10 text-gray-700 hover:bg-gray-50 dark:bg-white/5 dark:ring-white/20 dark:text-gray-300 dark:hover:bg-white/10' => $activeFilterCount === 0,
                            'crm-kanban-filter-active' => $activeFilterCount > 0,
                        ])
                    >
                        <x-filament::icon icon="heroicon-m-funnel" class="h-5 w-5" />
                        @if($activeFilterCount > 0)
                            <span class="absolute -top-1 -end-1 flex h-4 w-4 items-center justify-center rounded-full bg-primary-500 text-[10px] font-bold text-white">
                                {{ $activeFilterCount }}
                            </span>
                        @endif
                    </button>

                    <div
                        x-show="open"
                        x-on:click.outside="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        id="kanban-filters-panel"
                        role="dialog"
                        aria-label="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.heading') }}"
                        class="crm-kanban-filters-panel rounded-xl bg-white p-4 shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
                        x-cloak
                    >
                        <div class="mb-3 flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-950 dark:text-white">{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.heading') }}</h4>
                            @if($activeFilterCount > 0)
                                <button
                                    type="button"
                                    wire:click="resetKanbanFilters"
                                    class="text-xs text-danger-600 hover:text-danger-500 dark:text-danger-400"
                                >
                                    {{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.reset') }}
                                </button>
                            @endif
                        </div>
                        {{ $this->kanbanFiltersForm }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if($hasLoading)
        <div class="crm-kanban-shimmer" aria-busy="true">
            @include('crm::filament.kanban.shimmer-columns')
        </div>
    @endif

    <div class="crm-kanban-columns relative flex gap-4 items-start overflow-x-auto pb-4" wire:ignore.self>
        <script>window.crmFitKanbanBoard?.(document.currentScript.closest('.crm-kanban-board'))</script>
        @forelse($columns as $column)
            @include($board->getColumnView(), ['column' => $column, 'board' => $board])
        @empty
            <div class="crm-kanban-empty crm-kanban-empty-board">
                <x-filament::icon icon="heroicon-o-inbox" class="h-10 w-10 text-gray-300 dark:text-gray-600" />
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ __('crm::filament/kanban.empty.board-heading') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('crm::filament/kanban.empty.board-description') }}</p>
            </div>
        @endforelse

        @if(method_exists($this, 'canAddKanbanColumn') && $this->canAddKanbanColumn())
            <button
                type="button"
                x-data="{ opening: false }"
                x-on:click="if (opening) return; opening = true; $wire.mountAction('addKanbanColumn').finally(() => opening = false)"
                x-bind:aria-busy="opening"
                x-bind:class="{ 'crm-kanban-add-column-opening': opening }"
                style="width: {{ $board->getColumnWidth() }};"
                class="crm-kanban-add-column group flex shrink-0 cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 p-4 text-gray-400 transition hover:border-primary-400 hover:bg-primary-50/40 hover:text-primary-600 dark:border-white/15 dark:text-gray-500 dark:hover:bg-primary-400/5 dark:hover:text-primary-400"
            >
                <span class="flex h-14 w-14 items-center justify-center rounded-full transition group-hover:bg-primary-50 dark:group-hover:bg-primary-400/10">
                    <x-filament::icon icon="heroicon-o-plus" class="h-10 w-10" x-show="! opening" />
                    <x-filament::loading-indicator class="h-10 w-10" x-cloak x-show="opening" />
                </span>
                <span class="text-sm font-semibold">{{ $this->getAddKanbanColumnLabel() }}</span>
            </button>
        @endif
    </div>


    <x-filament-actions::modals />
</div>
