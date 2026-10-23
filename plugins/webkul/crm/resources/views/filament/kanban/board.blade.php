@php
    $board = $this->getKanbanBoard();
    $columns = $this->getKanbanColumns();
    $hasFilters = $board->hasFilters();
    $hasSearch = $board->isSearchable();
    $hasLoading = $board->hasLoading();
    $activeFilterCount = collect($this->kanbanFilters)->filter(fn ($v) => filled($v))->count();
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
    class="fi-kanban-board"
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
                            'bg-primary-50 ring-primary-200 text-primary-700 hover:bg-primary-100 dark:bg-primary-400/10 dark:ring-primary-400/30 dark:text-primary-400' => $activeFilterCount > 0,
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

    {{-- Loading overlay --}}
    @if($hasLoading)
        <div wire:loading.delay class="fi-kanban-loading absolute inset-0 z-10 flex items-center justify-center bg-white/60 dark:bg-gray-900/60 rounded-xl">
            <x-filament::loading-indicator class="h-8 w-8 text-primary-500" />
        </div>
    @endif

    <div class="relative flex gap-4 items-start overflow-x-auto pb-4" style="min-height: 60vh;" wire:ignore.self>
        @foreach($columns as $column)
            @include($board->getColumnView(), ['column' => $column, 'board' => $board])
        @endforeach

        @if(method_exists($this, 'canAddKanbanColumn') && $this->canAddKanbanColumn())
            <div
                x-data="{ adding: false, name: '', saving: false }"
                style="flex: 0 0 {{ $board->getColumnWidth() }}; width: {{ $board->getColumnWidth() }}; padding: 2px;"
                class="crm-kanban-add-column"
            >
                <div x-show="! adding">
                    <x-filament::button
                        color="gray"
                        outlined
                        icon="heroicon-m-plus"
                        x-on:click="adding = true; $nextTick(() => $refs.newColumnName.focus())"
                        style="width: 100%; justify-content: flex-start;"
                    >
                        {{ $this->getAddKanbanColumnLabel() }}
                    </x-filament::button>
                </div>

                <div
                    x-show="adding"
                    x-cloak
                    class="rounded-xl bg-gray-50 p-3 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10"
                >
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            x-ref="newColumnName"
                            x-model="name"
                            maxlength="255"
                            :placeholder="__('crm::filament/kanban.add-column.placeholder')"
                            :aria-label="$this->getAddKanbanColumnLabel()"
                            x-on:keydown.enter.prevent="if (! saving && name.trim()) { saving = true; $wire.addKanbanColumn(name).then((created) => { saving = false; if (created) { name = ''; $refs.newColumnName.focus(); } }) }"
                            x-on:keydown.escape="adding = false; name = ''"
                        />
                    </x-filament::input.wrapper>

                    <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                        <x-filament::button
                            size="sm"
                            x-bind:disabled="saving || ! name.trim()"
                            x-on:click="saving = true; $wire.addKanbanColumn(name).then((created) => { saving = false; if (created) { name = ''; $refs.newColumnName.focus(); } })"
                        >
                            {{ __('crm::filament/kanban.add-column.add') }}
                        </x-filament::button>

                        <x-filament::button
                            size="sm"
                            color="gray"
                            x-on:click="adding = false; name = ''"
                        >
                            {{ __('crm::filament/kanban.add-column.cancel') }}
                        </x-filament::button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <x-filament-actions::modals />
</div>
