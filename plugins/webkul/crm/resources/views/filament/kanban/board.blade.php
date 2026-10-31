@php
    use Webkul\Crm\Enums\KanbanActivityState;

    $board = $this->getKanbanBoard();
    $columns = $this->getKanbanColumns();
    $hasFilters = $board->hasFilters();
    $hasSearch = $board->isSearchable();
    $hasLoading = $board->hasLoading();
    $defaultKanbanFilters = method_exists($this, 'defaultKanbanFilters') ? $this->defaultKanbanFilters() : [];
    $activeFilterCount = collect($this->kanbanFilters)->filter(fn ($v, $key) => filled($v) && $v !== ($defaultKanbanFilters[$key] ?? null))->count();
    $textColors = [
        'danger'  => 'text-danger-600 dark:text-danger-400',
        'warning' => 'text-warning-600 dark:text-warning-400',
        'success' => 'text-success-600 dark:text-success-400',
        'gray'    => 'text-gray-400 dark:text-gray-500',
    ];
    $progressColors = [
        KanbanActivityState::PLANNED->value => 'bg-success-500',
        KanbanActivityState::TODAY->value   => 'bg-warning-400',
        KanbanActivityState::OVERDUE->value => 'bg-danger-500',
        KanbanActivityState::NONE->value    => 'bg-gray-300 dark:bg-gray-600',
    ];
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
    @if($hasFilters
        || $hasSearch)
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
            @include('crm::filament.kanban.shimmer')
        </div>
    @endif

    <div class="crm-kanban-columns relative flex gap-4 items-start overflow-x-auto pb-4" wire:ignore.self>
        <script>window.crmFitKanbanBoard?.(document.currentScript.closest('.crm-kanban-board'))</script>
        @forelse($columns as $column)
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

            $colorKey = in_array($color, ['primary', 'success', 'warning', 'danger', 'info'], true) ? $color : 'gray';
            $colorClasses = 'crm-kanban-column-'.$colorKey;
            $badgeColorClasses = 'crm-kanban-count-'.($isOverWip ? 'danger' : $colorKey);
        @endphp

        <div
            wire:key="kanban-column-{{ $column->value }}"
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
                        data-kanban-count="{{ $column->count }}"
                        @if($wipLimit) data-kanban-wip-limit="{{ $wipLimit }}" @endif
                        class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeColorClasses }}"
                        @if($isOverWip) title="Over WIP limit ({{ $wipLimit }})" @endif
                    >
                        {{ $column->count }}@if($wipLimit) / {{ $wipLimit }}@endif
                    </span>

                    @if($canEditColumn
                        || $canDeleteColumn)
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
                $progressStates = collect(KanbanActivityState::cases())->mapWithKeys(fn (KanbanActivityState $state): array => [$state->value => [$progressColors[$state->value], $state->getLabel()]])->all();
            @endphp

            @if($summary
                || $progressTotal)
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
                    @php
                        $card = $this->getKanbanCard($record);
                        $recordKey = $record->getKey();
                    @endphp

                    <div
                        wire:key="kanban-card-{{ $recordKey }}"
                        data-kanban-card
                        data-record-id="{{ $recordKey }}"
                        role="listitem"
                        aria-label="{{ $card->title }}"
                        @if ($card->tooltip) title="{{ $card->tooltip }}" @endif
                        @if ($card->menu)
                            x-data="{ menusReady: false }"
                            x-on:pointerenter.once="menusReady = true"
                            x-on:focusin.once="menusReady = true"
                        @endif
                        @if ($card->url)
                            x-on:click="if (! (typeof dragStarted !== 'undefined' && dragStarted) && ! $event.target.closest('[data-card-control]')) window.location.href = @js($card->url)"
                        @endif
                        @class([
                            'fi-kanban-card group relative flex gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-950/5 transition-shadow hover:shadow-md dark:bg-gray-900 dark:ring-white/10',
                            'cursor-pointer' => $card->url,
                        ])
                    >
                        @if ($card->avatar)
                            <span class="crm-contact-avatar crm-contact-avatar-sm">
                                @if ($card->avatar['url'])
                                    <img src="{{ $card->avatar['url'] }}" alt="{{ $card->title }}" loading="lazy" />
                                @else
                                    <x-filament::icon :icon="$card->avatar['icon']" class="crm-contact-avatar-icon" />
                                @endif
                            </span>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div @class(['flex items-start gap-2', 'justify-between' => $card->badge, 'pe-6' => $card->menu])>
                                @if ($card->icon)
                                    <x-filament::icon :icon="$card->icon['icon']" @class(['mt-0.5 h-4 w-4 shrink-0', $textColors[$card->icon['color']] ?? '']) title="{{ $card->icon['tooltip'] }}" />
                                @endif

                                <h4 @class(['min-w-0 text-sm font-semibold text-gray-950 dark:text-white', 'truncate' => $card->avatar]) @if ($card->titleTooltip) title="{{ $card->titleTooltip }}" @endif>
                                    {{ $card->title }}
                                </h4>

                                @if ($card->badge)
                                    <x-filament::badge size="sm" :color="$card->badge['color']">
                                        {{ $card->badge['label'] }}
                                    </x-filament::badge>
                                @endif
                            </div>

                            @foreach ($card->lines as $line)
                                <p
                                    @class([
                                        'mt-1 flex items-center gap-1.5',
                                        'text-xs' => $line['small'],
                                        'text-sm' => ! $line['small'],
                                        'text-gray-700 dark:text-gray-300' => $line['strong'],
                                        'text-gray-600 dark:text-gray-400' => ! $line['strong'],
                                    ])
                                    @if ($line['tooltip']) title="{{ $line['tooltip'] }}" @endif
                                >
                                    @if ($line['icon'])
                                        <x-filament::icon :icon="$line['icon']" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                                    @endif

                                    <span class="truncate">{{ $line['text'] }}</span>

                                    @if ($line['suffix'])
                                        <span class="shrink-0 text-xs text-gray-500">{{ $line['suffix'] }}</span>
                                    @endif
                                </p>
                            @endforeach

                            @if ($card->tags)
                                <div class="mt-2 flex flex-wrap gap-1" title="{{ $card->tags[0]['tooltip'] }}">
                                    @foreach ($card->tags as $tag)
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            style="background-color: {{ $tag['color'] ?: '#9ca3af' }}26; color: {{ $tag['color'] ?: '#4b5563' }};"
                                        >
                                            {{ $tag['name'] }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($card->start
                                || $card->end)
                                <div class="mt-2.5 flex items-center justify-between gap-2">
                                    @foreach ([$card->start, $card->end] as $side => $items)
                                        <div @class(['flex items-center', 'min-w-0 gap-2' => $side === 0, 'shrink-0 gap-1.5' => $side === 1])>
                                            @foreach ($items as $item)
                                                @switch ($item['type'])
                                                    @case ('stars')
                                                        <div data-card-control class="flex items-center" title="{{ $item['tooltip'] }}">
                                                            @foreach ($item['labels'] as $star => $starLabel)
                                                                <button
                                                                    type="button"
                                                                    @if ($item['editable']) wire:click="setKanbanCardPriority('{{ $item['record'] }}', {{ $star }})" @else disabled @endif
                                                                    title="{{ $starLabel }}"
                                                                    aria-label="{{ $item['label'] }}: {{ $starLabel }}"
                                                                    @class(['p-0.5', 'hover:scale-110' => $item['editable'], 'cursor-default' => ! $item['editable']])
                                                                >
                                                                    <x-filament::icon :icon="$star <= $item['value'] ? 'heroicon-s-star' : 'heroicon-o-star'" @class(['h-4 w-4', 'text-warning-400' => $star <= $item['value'], 'text-gray-300 dark:text-gray-600' => $star > $item['value']]) />
                                                                </button>
                                                            @endforeach
                                                        </div>

                                                        @break

                                                    @case ('button')
                                                        <button
                                                            type="button"
                                                            data-card-control
                                                            wire:click="{{ $item['click'] }}"
                                                            @class([
                                                                'flex items-center rounded-md',
                                                                'p-0.5 hover:bg-gray-100 dark:hover:bg-white/10 '.($textColors[$item['color']] ?? '') => $item['color'],
                                                                'p-1 text-gray-400 transition hover:bg-success-50 hover:text-success-600 dark:hover:bg-success-400/10' => ! $item['color'],
                                                            ])
                                                            title="{{ $item['tooltip'] }}"
                                                            aria-label="{{ $item['label'] }}"
                                                        >
                                                            <x-filament::icon :icon="$item['icon']" class="h-4 w-4" />
                                                        </button>

                                                        @break

                                                    @case ('text')
                                                        <span
                                                            @class(['flex items-center gap-1 text-xs', 'font-medium '.($textColors[$item['color']] ?? '') => $item['color'], 'text-gray-500 dark:text-gray-400' => ! $item['color']])
                                                            @if ($item['tooltip']) title="{{ $item['tooltip'] }}" @endif
                                                        >
                                                            @if ($item['icon'])
                                                                <x-filament::icon :icon="$item['icon']" class="h-3.5 w-3.5" />
                                                            @endif

                                                            {{ $item['text'] }}
                                                        </span>

                                                        @break

                                                    @case ('amount')
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-medium text-gray-950 dark:text-white" title="{{ $item['tooltip'] }}">{{ $item['amount'] }}</p>

                                                            @if ($item['date'])
                                                                <p class="text-xs text-gray-500 dark:text-gray-400" title="{{ $item['dateTooltip'] }}">{{ $item['date'] }}</p>
                                                            @endif
                                                        </div>

                                                        @break

                                                    @case ('dot')
                                                        <span class="crm-kanban-color-dot" style="background-color: {{ $item['color'] }};" title="{{ $item['tooltip'] }}"></span>

                                                        @break

                                                    @case ('status')
                                                        <x-filament::badge size="sm" :color="$item['color']">
                                                            {{ $item['label'] }}
                                                        </x-filament::badge>

                                                        @break

                                                    @case ('user')
                                                        <span class="crm-kanban-avatar flex h-6 w-6 shrink-0 items-center justify-center overflow-hidden rounded-full text-[10px] font-semibold" title="{{ $item['tooltip'] }}">
                                                            @if ($item['avatar'])
                                                                <img src="{{ $item['avatar'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover" />
                                                            @else
                                                                {{ $item['initials'] }}
                                                            @endif
                                                        </span>

                                                        @break
                                                @endswitch
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if ($card->menu)
                            <div data-card-control class="absolute end-2 top-2 z-10 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                                <template x-if="! menusReady">
                                    <button type="button" class="flex rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10" title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.menu') }}" aria-label="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.menu') }}">
                                        <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="h-4 w-4" />
                                    </button>
                                </template>

                                <template x-if="menusReady">
                                    <div>
                                        <x-filament::dropdown placement="bottom-end" teleport width="xs">
                                            <x-slot name="trigger">
                                                <button type="button" class="flex rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10" title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.menu') }}" aria-label="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.menu') }}">
                                                    <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="h-4 w-4" />
                                                </button>
                                            </x-slot>

                                            <x-filament::dropdown.list>
                                                @foreach ($card->menu as $item)
                                                    @if ($item['url'])
                                                        <x-filament::dropdown.list.item :icon="$item['icon']" :color="$item['color'] ?? 'gray'" tag="a" :href="$item['url']">
                                                            {{ $item['label'] }}
                                                        </x-filament::dropdown.list.item>
                                                    @else
                                                        <x-filament::dropdown.list.item :icon="$item['icon']" :color="$item['color'] ?? 'gray'" wire:click="{{ $item['click'] }}">
                                                            {{ $item['label'] }}
                                                        </x-filament::dropdown.list.item>
                                                    @endif
                                                @endforeach
                                            </x-filament::dropdown.list>
                                        </x-filament::dropdown>
                                    </div>
                                </template>
                            </div>
                        @endif
                    </div>
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
        @empty
            <div class="crm-kanban-empty crm-kanban-empty-board">
                <x-filament::icon icon="heroicon-o-inbox" class="h-10 w-10 text-gray-300 dark:text-gray-600" />
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ __('crm::filament/kanban.empty.board-heading') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('crm::filament/kanban.empty.board-description') }}</p>
            </div>
        @endforelse

        @if(method_exists($this, 'canAddKanbanColumn')
            && $this->canAddKanbanColumn())
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
