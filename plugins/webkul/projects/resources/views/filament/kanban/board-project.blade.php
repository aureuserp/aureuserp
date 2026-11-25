@php
    use Webkul\Project\Enums\TaskState;
    use Webkul\Project\Filament\Resources\TaskResource;

    $columns = $this->getKanbanColumns();
    $grouping = $this->getKanbanGrouping();
    $canManageProjects = $this->canManageProjects();
    $states = TaskState::cases();
    $stateColors = [
        'gray'    => '#6b7280',
        'warning' => '#f59e0b',
        'primary' => 'var(--primary-500)',
        'danger'  => '#ef4444',
        'success' => '#22c55e',
    ];
    $stateProgressColors = [
        'gray'    => 'bg-gray-400 dark:bg-gray-500',
        'warning' => 'bg-warning-500',
        'primary' => 'bg-primary-500',
        'danger'  => 'bg-danger-500',
        'success' => 'bg-success-500',
    ];
    $activityIconColors = [
        'gray'    => 'text-gray-400 hover:text-primary-600 dark:hover:text-primary-400',
        'danger'  => 'text-danger-500',
        'warning' => 'text-warning-500',
        'success' => 'text-success-500',
    ];
@endphp

<div
    x-load
    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-kanban', 'wezlo/filament-kanban') }}"
    x-data="kanbanBoard({ hasOrderColumn: true })"
    wire:loading.class="projects-kanban-loading"
    wire:target="callMountedAction,callMountedTableAction"
    class="fi-kanban-board projects-kanban-board relative"
>
    <div class="projects-kanban-shimmer" aria-hidden="true">
        <div class="projects-board-shimmer-columns">
            @for ($i = 0; $i < 4; $i++)
                <div class="projects-board-shimmer-column">
                    <div class="projects-board-shimmer-line projects-board-shimmer-title"></div>

                    @for ($j = 0; $j < 4; $j++)
                        <div class="projects-board-shimmer-card">
                            <div class="projects-board-shimmer-line projects-board-shimmer-bar"></div>
                            <div class="projects-board-shimmer-line projects-board-shimmer-title"></div>
                        </div>
                    @endfor
                </div>
            @endfor
        </div>
    </div>

    <div class="projects-kanban-columns relative flex items-start gap-4 overflow-x-auto pb-4" wire:ignore.self>
        @forelse ($columns as $column)
            @php
                $tasks = $column['tasks'];
                $progress = $this->kanbanStateProgress($tasks);
                $progressTotal = array_sum($progress);
            @endphp

            <div
                wire:key="projects-kanban-project-column-{{ $column['value'] }}"
                class="fi-kanban-column projects-kanban-column-gray w-80 flex shrink-0 flex-col rounded-xl border"
                role="group"
                aria-label="{{ $column['label'] }}"
            >
                <div class="flex items-center justify-between gap-2 p-3">
                    <h3 class="flex min-w-0 flex-1 items-center gap-1 truncate text-sm font-semibold text-gray-950 dark:text-white">
                        @if (in_array($column['type'], ['private', 'none'], true))
                            <x-filament::icon icon="heroicon-m-lock-closed" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                        @endif
                        <span class="truncate">{{ $column['label'] }}</span>
                    </h3>

                    <div class="flex shrink-0 items-center gap-1.5">
                        @if ($grouping === 'project' && $column['type'] === 'private')
                            <button
                                type="button"
                                wire:click="mountAction('quickCreatePrivateTask')"
                                class="rounded-md p-0.5 text-gray-400 transition hover:bg-gray-200/50 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-white/10 dark:hover:text-gray-300"
                                title="{{ __('projects::filament/kanban.card.quick-create') }}"
                            >
                                <x-filament::icon icon="heroicon-m-plus" class="h-4 w-4" />
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="mountAction('quickCreateTask', { group: '{{ $grouping }}', value: '{{ $column['value'] }}' })"
                                class="rounded-md p-0.5 text-gray-400 transition hover:bg-gray-200/50 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-white/10 dark:hover:text-gray-300"
                                title="{{ __('projects::filament/kanban.card.quick-create') }}"
                            >
                                <x-filament::icon icon="heroicon-m-plus" class="h-4 w-4" />
                            </button>
                        @endif

                        <span data-kanban-count class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-medium projects-kanban-count-gray">
                            {{ $tasks->count() }}
                        </span>

                        @if ($grouping === 'project' && $column['type'] === 'project' && $canManageProjects)
                            <x-filament::dropdown placement="bottom-end" teleport>
                                <x-slot name="trigger">
                                    <button
                                        type="button"
                                        class="rounded-md p-0.5 text-gray-400 transition hover:bg-gray-200/50 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-white/10 dark:hover:text-gray-300"
                                        title="{{ __('projects::filament/kanban.column.project-settings') }}"
                                    >
                                        <x-filament::icon icon="heroicon-m-cog-6-tooth" class="h-4 w-4" />
                                    </button>
                                </x-slot>

                                <x-filament::dropdown.list>
                                    <x-filament::dropdown.list.item
                                        icon="heroicon-m-pencil-square"
                                        wire:click="mountAction('editProject', { project: '{{ $column['value'] }}' })"
                                    >
                                        {{ __('projects::filament/kanban.column.edit-project') }}
                                    </x-filament::dropdown.list.item>

                                    <x-filament::dropdown.list.item
                                        icon="heroicon-m-trash"
                                        color="danger"
                                        wire:click="mountAction('deleteProject', { project: '{{ $column['value'] }}' })"
                                    >
                                        {{ __('projects::filament/kanban.column.delete-project') }}
                                    </x-filament::dropdown.list.item>
                                </x-filament::dropdown.list>
                            </x-filament::dropdown>
                        @endif
                    </div>
                </div>

                @if ($progressTotal > 0)
                    <div class="flex items-center gap-3 px-3 pb-2">
                        <div
                            data-kanban-progress
                            class="flex h-2.5 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-white/10"
                            role="img"
                            aria-label="{{ __('projects::filament/kanban.progress.label') }}"
                        >
                            @foreach ($states as $state)
                                @if (($progress[$state->value] ?? 0) > 0)
                                    <span
                                        class="h-full {{ $stateProgressColors[$state->getColor()] ?? 'bg-gray-400' }} transition"
                                        style="width: {{ round($progress[$state->value] / $progressTotal * 100, 2) }}%;"
                                        title="{{ $state->getLabel() }}: {{ $progress[$state->value] }}"
                                    ></span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <div
                    data-kanban-column
                    data-column-value="{{ $column['value'] }}"
                    class="fi-kanban-cards relative flex min-h-16 grow flex-col gap-2.5 overflow-y-auto p-2.5"
                    role="list"
                >
                    @forelse ($tasks as $task)
                        @php
                            $state = $task->state;
                            $activity = $this->taskActivityIndicator($task);
                            $visibleTags = $task->tags->take(3);
                            $hiddenTags = $task->tags->slice(3);
                            $viewUrl = TaskResource::getUrl('view', ['record' => $task->getKey()]);
                            $editUrl = TaskResource::getUrl('edit', ['record' => $task->getKey()]);
                        @endphp

                        <div
                            wire:key="projects-kanban-card-{{ $task->getKey() }}"
                            data-kanban-card
                            data-record-id="{{ $task->getKey() }}"
                            data-task-state="{{ $task->state?->value }}"
                            role="listitem"
                            aria-label="{{ $task->title }}"
                            class="fi-kanban-card group relative rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md dark:bg-gray-900 dark:ring-white/10"
                        >
                            <div class="absolute end-1.5 top-1.5 opacity-0 transition group-hover:opacity-100" data-card-control>
                                <x-filament::dropdown placement="bottom-end" teleport>
                                    <x-slot name="trigger">
                                        <button
                                            type="button"
                                            class="rounded-md p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10 dark:hover:text-gray-300"
                                            title="{{ __('projects::filament/kanban.card.title') }}"
                                        >
                                            <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="h-4 w-4" />
                                        </button>
                                    </x-slot>

                                    <x-filament::dropdown.list>
                                        <x-filament::dropdown.list.item icon="heroicon-m-eye" tag="a" :href="$viewUrl">
                                            {{ __('projects::filament/kanban.card.view') }}
                                        </x-filament::dropdown.list.item>

                                        <x-filament::dropdown.list.item icon="heroicon-m-pencil-square" tag="a" :href="$editUrl">
                                            {{ __('projects::filament/kanban.card.edit') }}
                                        </x-filament::dropdown.list.item>

                                        <x-filament::dropdown.list.item icon="heroicon-m-link" wire:click="shareTask('{{ $task->getKey() }}')">
                                            {{ __('projects::filament/kanban.card.share') }}
                                        </x-filament::dropdown.list.item>

                                        <x-filament::dropdown.list.item
                                            icon="heroicon-m-trash"
                                            color="danger"
                                            wire:click="mountAction('deleteTask', { task: '{{ $task->getKey() }}' })"
                                        >
                                            {{ __('projects::filament/kanban.card.delete') }}
                                        </x-filament::dropdown.list.item>
                                    </x-filament::dropdown.list>
                                </x-filament::dropdown>
                            </div>

                            <div class="flex items-start gap-1.5 pe-6">
                                <button
                                    type="button"
                                    wire:click="toggleTaskPriority('{{ $task->getKey() }}')"
                                    data-card-control
                                    class="mt-0.5 shrink-0"
                                    title="{{ __('projects::filament/kanban.card.priority') }}"
                                >
                                    <x-filament::icon
                                        :icon="$task->priority ? 'heroicon-s-star' : 'heroicon-o-star'"
                                        @class(['h-4 w-4', 'text-warning-400' => $task->priority, 'text-gray-300 dark:text-gray-600' => ! $task->priority])
                                    />
                                </button>

                                <a
                                    href="{{ $viewUrl }}"
                                    class="min-w-0 flex-1 text-sm font-semibold text-gray-950 hover:text-primary-600 dark:text-white dark:hover:text-primary-400"
                                >
                                    {{ $task->title }}
                                </a>
                            </div>

                            @if ($task->partner)
                                <p class="mt-1 flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                    <x-filament::icon icon="heroicon-m-user" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                                    <span class="truncate">{{ $task->partner->name }}</span>
                                </p>
                            @endif

                            @if ($task->milestone)
                                <p class="mt-0.5 flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                    <x-filament::icon icon="heroicon-m-flag" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                                    <span class="truncate">{{ $task->milestone->name }}</span>
                                </p>
                            @endif

                            @if ($task->tags->isNotEmpty())
                                <div class="mt-2 flex min-w-0 flex-nowrap items-center gap-1 overflow-hidden">
                                    @foreach ($visibleTags as $tag)
                                        <span
                                            class="inline-flex max-w-24 shrink-0 items-center truncate rounded-full px-1.5 py-0.5 text-[10px] font-medium leading-tight"
                                            style="background-color: {{ $tag->color ?: '#9ca3af' }}26; color: {{ $tag->color ?: '#4b5563' }};"
                                            title="{{ $tag->name }}"
                                        >
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach

                                    @if ($hiddenTags->isNotEmpty())
                                        <span
                                            class="inline-flex shrink-0 items-center rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium leading-tight text-gray-500 dark:bg-white/10 dark:text-gray-400"
                                            title="{{ $hiddenTags->pluck('name')->implode(', ') }}"
                                        >
                                            {{ __('projects::filament/kanban.card.tags-more', ['count' => $hiddenTags->count()]) }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-3 flex items-center gap-2">
                                @if (TaskResource::canEdit($task) || $task->activities->isNotEmpty())
                                    <button
                                        type="button"
                                        wire:click="mountAction('{{ $task->activities->isEmpty() ? 'scheduleTaskActivity' : 'taskActivities' }}', { record: '{{ $task->getKey() }}' })"
                                        data-card-control
                                        @class(['shrink-0 transition', $activityIconColors[$activity['color']] ?? $activityIconColors['gray']])
                                        title="{{ __('chatter::filament/resources/actions/chatter/activity-action.setup.title') }}"
                                    >
                                        <x-filament::icon :icon="$activity['icon']" class="h-4 w-4" />
                                    </button>
                                @endif

                                @if ($task->allocated_hours)
                                    <span class="inline-flex items-center gap-1 rounded-md border border-gray-200 px-1.5 py-0.5 text-[11px] font-semibold text-gray-600 dark:border-white/10 dark:text-gray-300">
                                        <x-filament::icon icon="heroicon-m-clock" class="h-3.5 w-3.5" />
                                        {{ $task->allocated_hours }}h
                                    </span>
                                @endif

                                @if ($task->deadline)
                                    <span @class([
                                        'inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-[11px] font-semibold',
                                        'bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400' => $task->deadline->isPast(),
                                        'text-gray-500 dark:text-gray-400' => ! $task->deadline->isPast(),
                                    ])>
                                        <x-filament::icon icon="heroicon-m-calendar" class="h-3.5 w-3.5" />
                                        {{ $task->deadline->isoFormat('MMM D') }}
                                    </span>
                                @endif

                                <div class="ms-auto flex shrink-0 items-center gap-1.5" data-card-control>
                                    @if ($state)
                                        <x-filament::dropdown placement="bottom-end" teleport>
                                            <x-slot name="trigger">
                                                <button
                                                    type="button"
                                                    class="flex rounded-full p-0.5 transition hover:bg-gray-100 dark:hover:bg-white/10"
                                                    title="{{ $state->getLabel() }}"
                                                >
                                                    <x-filament::icon
                                                        :icon="$state->getIcon()"
                                                        class="h-5 w-5"
                                                        style="color: {{ $stateColors[$state->getColor()] ?? '#6b7280' }};"
                                                    />
                                                </button>
                                            </x-slot>

                                            <x-filament::dropdown.list>
                                                @foreach ($states as $option)
                                                    <x-filament::dropdown.list.item
                                                        :icon="$option->getIcon()"
                                                        :color="$option->getColor()"
                                                        wire:click="setTaskState('{{ $task->getKey() }}', '{{ $option->value }}')"
                                                    >
                                                        {{ $option->getLabel() }}
                                                    </x-filament::dropdown.list.item>
                                                @endforeach
                                            </x-filament::dropdown.list>
                                        </x-filament::dropdown>
                                    @endif

                                    <div class="flex items-center">
                                        @if ($task->users->isNotEmpty())
                                            @include('projects::filament.kanban.partials.assignees', ['task' => $task])
                                        @endif

                                        @if ($task->users->isEmpty() && TaskResource::canEdit($task))
                                            <button
                                                type="button"
                                                wire:click="mountAction('assignTask', { record: '{{ $task->getKey() }}' })"
                                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-primary-600 dark:text-gray-500 dark:hover:bg-white/10 dark:hover:text-primary-400"
                                                title="{{ __('projects::filament/kanban.assign.label') }}"
                                            >
                                                <x-filament::icon icon="heroicon-m-user-plus" class="h-4 w-4" />
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="projects-kanban-empty projects-kanban-empty-column">
                            <x-filament::icon icon="heroicon-o-inbox" class="h-8 w-8 text-gray-300 dark:text-gray-600" />
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ __('projects::filament/kanban.column.empty-heading') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('projects::filament/kanban.column.empty-description') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="projects-kanban-empty projects-kanban-empty-board w-full">
                <x-filament::icon icon="heroicon-o-inbox" class="h-10 w-10 text-gray-300 dark:text-gray-600" />
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ __('projects::filament/kanban.empty.heading') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('projects::filament/kanban.empty.description') }}</p>
            </div>
        @endforelse

        @if ($grouping === 'project' && \Webkul\Project\Filament\Resources\ProjectResource::canCreate())
            <button
                type="button"
                wire:click="mountAction('addProject')"
                class="projects-kanban-add-column group flex w-80 shrink-0 flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 p-4 text-gray-400 transition hover:border-primary-400 hover:bg-primary-50/40 hover:text-primary-600 dark:border-white/15 dark:text-gray-500 dark:hover:bg-primary-400/5 dark:hover:text-primary-400"
            >
                <span class="flex h-14 w-14 items-center justify-center rounded-full transition group-hover:bg-primary-50 dark:group-hover:bg-primary-400/10">
                    <x-filament::icon icon="heroicon-o-plus" class="h-10 w-10" />
                </span>
                <span class="text-sm font-semibold">{{ __('projects::filament/kanban.column.add-project') }}</span>
            </button>
        @endif
    </div>

    <x-filament-actions::modals />
</div>

@once
    <script>
        document.addEventListener('livewire:init', () => {
            window.Livewire.on('kanban-stats-refresh', () => {
                requestAnimationFrame(() => {
                    const order = ['in_progress', 'change_requested', 'approved', 'cancelled', 'done'];
                    const colors = {
                        in_progress: 'bg-gray-400 dark:bg-gray-500',
                        change_requested: 'bg-warning-500',
                        approved: 'bg-primary-500',
                        cancelled: 'bg-danger-500',
                        done: 'bg-success-500',
                    };

                    document.querySelectorAll('.projects-kanban-board .fi-kanban-column').forEach((column) => {
                        const cards = column.querySelectorAll('[data-kanban-card]');
                        const total = cards.length;

                        const badge = column.querySelector('[data-kanban-count]');

                        if (badge) {
                            badge.textContent = total;
                        }

                        const bar = column.querySelector('[data-kanban-progress]');

                        if (! bar) {
                            return;
                        }

                        const tally = {};
                        order.forEach((state) => (tally[state] = 0));
                        cards.forEach((card) => {
                            const state = card.dataset.taskState;

                            if (state && tally[state] !== undefined) {
                                tally[state]++;
                            }
                        });

                        bar.innerHTML = '';

                        if (total === 0) {
                            return;
                        }

                        order.forEach((state) => {
                            if (tally[state] > 0) {
                                const span = document.createElement('span');
                                span.className = 'h-full transition ' + colors[state];
                                span.style.width = ((tally[state] / total) * 100).toFixed(2) + '%';
                                bar.appendChild(span);
                            }
                        });
                    });
                });
            });

            if (! window.__projectsKanbanAutoScroll) {
                window.__projectsKanbanAutoScroll = true;

                const EDGE = 72;
                const SPEED = 22;
                let pointerX = null;
                let raf = null;

                const container = () => document.querySelector('.projects-kanban-columns');
                const dragging = () => !! document.querySelector('.fi-kanban-card-ghost');

                const loop = () => {
                    if (! dragging()) {
                        raf = null;

                        return;
                    }

                    const el = container();

                    if (el && pointerX !== null) {
                        const rect = el.getBoundingClientRect();

                        if (pointerX > rect.right - EDGE) {
                            el.scrollLeft += SPEED;
                        } else if (pointerX < rect.left + EDGE) {
                            el.scrollLeft -= SPEED;
                        }
                    }

                    raf = requestAnimationFrame(loop);
                };

                const track = (event) => {
                    pointerX = event.touches && event.touches[0] ? event.touches[0].clientX : event.clientX;

                    if (dragging() && ! raf) {
                        raf = requestAnimationFrame(loop);
                    }
                };

                ['dragover', 'pointermove', 'touchmove'].forEach((name) => {
                    document.addEventListener(name, track, { capture: true, passive: true });
                });
            }
        });
    </script>
@endonce
