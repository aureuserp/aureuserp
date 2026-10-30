@php
    use Illuminate\Support\Number;
    use Webkul\Crm\Enums\ActivityDueStatus;
    use Webkul\Crm\Enums\OpportunityPriority;
    use Webkul\Crm\Filament\Support\LeadResourceResolver;

    $recordId = $record->getKey();
    $url = $board->getRecordUrl($record);
    $resource = LeadResourceResolver::resourceFor($record);
    $canEdit = $resource::canEdit($record);
    $canDelete = $resource::canForceDelete($record);
    $colorHex = filled($record->color) ? $record->color : null;
    $status = $record->trashed() ? 'lost' : ($record->isWon() ? 'won' : null);
    $currency = $record->company?->currency?->code ?? 'USD';
    $priority = $record->priority?->value ?? 0;
    $maxPriority = OpportunityPriority::VERY_HIGH->value;

    $activityState = $record->activityState();
    $activities = $record->crmActivities->sortBy(fn ($activity) => $activity->date_deadline?->timestamp ?? PHP_INT_MAX)->values();
    $activityIcon = fn ($activity): string => str_starts_with((string) $activity?->activityType?->icon, 'heroicon-') ? $activity->activityType->icon : 'heroicon-o-clock';
    $activityColor = match ($activityState) {
        ActivityDueStatus::OVERDUE => 'text-danger-600 dark:text-danger-400',
        ActivityDueStatus::TODAY   => 'text-warning-600 dark:text-warning-400',
        ActivityDueStatus::PLANNED => 'text-success-600 dark:text-success-400',
        default                    => 'text-gray-400 dark:text-gray-500',
    };
    $activityCount = $activities->count();
    $activitySummary = match ($activityState) {
        ActivityDueStatus::OVERDUE => trans_choice('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activities-overdue', $activityCount, ['count' => $activityCount]),
        ActivityDueStatus::TODAY   => trans_choice('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activities-today', $activityCount, ['count' => $activityCount]),
        ActivityDueStatus::PLANNED => trans_choice('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activities-planned', $activityCount, ['count' => $activityCount]),
        default                    => null,
    };
    $activityTitle = $activitySummary
        ? $activitySummary.($activities->first()?->activityType ? ' · '.$activities->first()->activityType->name : '')
        : __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.no-activities');

    $shows = fn (string $column): bool => ! method_exists($this, 'isKanbanCardFieldVisible') || $this->isKanbanCardFieldVisible($column);

    $salesperson = $shows('user.name') ? $record->user : null;
    $initials = $salesperson ? collect(explode(' ', $salesperson->name))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') : null;
@endphp

<div
    wire:key="kanban-card-{{ $recordId }}"
    data-kanban-card
    data-record-id="{{ $recordId }}"
    role="listitem"
    aria-label="{{ $record->name }}"
    title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.open', ['name' => $record->name]) }}"
    x-data="{ menusReady: false }"
    x-on:pointerenter.once="menusReady = true"
    x-on:focusin.once="menusReady = true"
    x-on:click="if (! (typeof dragStarted !== 'undefined' && dragStarted) && ! $event.target.closest('[data-card-control]')) window.location.href = @js($url)"
    class="fi-kanban-card group relative cursor-pointer rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-950/5 transition-shadow hover:shadow-md dark:bg-gray-900 dark:ring-white/10"
>
    <div class="flex items-start justify-between gap-2">
        <h4 class="text-sm font-semibold text-gray-950 dark:text-white" title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.title') }}: {{ $record->name }}">
            {{ $record->name }}
        </h4>

        @if ($canEdit
            || $canDelete)
            <div data-card-control class="relative z-10 -me-1 -mt-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                <template x-if="! menusReady">
                    <button type="button" class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10" title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.menu') }}" aria-label="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.menu') }}">
                        <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="h-4 w-4" />
                    </button>
                </template>

                <template x-if="menusReady">
                    <div>
                <x-filament::dropdown placement="bottom-end" teleport width="xs">
                    <x-slot name="trigger">
                        <button type="button" class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10" title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.menu') }}" aria-label="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.menu') }}">
                            <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="h-4 w-4" />
                        </button>
                    </x-slot>

                    <x-filament::dropdown.list>
                        @if ($canEdit)
                            <x-filament::dropdown.list.item icon="heroicon-m-pencil-square" tag="a" :href="$resource::getUrl('edit', ['record' => $record])">
                                {{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.edit') }}
                            </x-filament::dropdown.list.item>
                        @endif

                        @if ($canEdit)
                            <x-filament::dropdown.list.item icon="heroicon-m-swatch" wire:click="mountAction('kanbanCardColor', { record: '{{ $recordId }}' })">
                                {{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.color') }}
                            </x-filament::dropdown.list.item>
                        @endif

                        @if ($canDelete)
                            <x-filament::dropdown.list.item icon="heroicon-m-trash" color="danger" wire:click="mountAction('deleteKanbanCard', { record: '{{ $recordId }}' })">
                                {{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.delete') }}
                            </x-filament::dropdown.list.item>
                        @endif
                    </x-filament::dropdown.list>
                </x-filament::dropdown>
                    </div>
                </template>
            </div>
        @endif
    </div>

    @if ($shows('expected_revenue')
        && (float) $record->expected_revenue > 0)
        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300" title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.expected-revenue') }}">
            {{ Number::currency((float) $record->expected_revenue, $currency) }}
            @if ((float) $record->recurring_revenue > 0)
                <span class="text-xs text-gray-500">+ {{ Number::currency((float) $record->recurring_revenue, $currency) }}{{ $record->recurringPlan ? ' '.$record->recurringPlan->name : '' }}</span>
            @endif
        </p>
    @endif

    @if ($shows('partner.name')
        && ($customer = $record->partner?->name ?? $record->contact_name ?? $record->company_name))
        <p class="mt-1 flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-400" title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.customer') }}">
            <x-filament::icon icon="heroicon-m-building-office-2" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
            <span class="truncate">{{ $customer }}</span>
        </p>
    @endif

    @if ($shows('tags.name')
        && $record->tags->isNotEmpty())
        <div class="mt-2 flex flex-wrap gap-1" title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.tags') }}">
            @foreach ($record->tags as $tag)
                <span
                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                    style="background-color: {{ $tag->color ?: '#9ca3af' }}26; color: {{ $tag->color ?: '#4b5563' }};"
                >
                    {{ $tag->name }}
                </span>
            @endforeach
        </div>
    @endif

    <div class="mt-2.5 flex items-center justify-between">
        <div class="flex items-center gap-2">
            @if ($shows('priority'))
                <div data-card-control class="flex items-center" title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.priority') }}: {{ $record->priority?->getLabel() }}">
                    @for ($star = 1; $star <= $maxPriority; $star++)
                        <button
                            type="button"
                            @if ($canEdit) wire:click="setKanbanCardPriority('{{ $recordId }}', {{ $star }})" @else disabled @endif
                            title="{{ OpportunityPriority::from($star)->getLabel() }}"
                            aria-label="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.priority') }}: {{ OpportunityPriority::from($star)->getLabel() }}"
                            class="p-0.5 {{ $canEdit ? 'hover:scale-110' : 'cursor-default' }}"
                        >
                            <x-filament::icon :icon="$star <= $priority ? 'heroicon-s-star' : 'heroicon-o-star'" @class(['h-4 w-4', 'text-warning-400' => $star <= $priority, 'text-gray-300 dark:text-gray-600' => $star > $priority]) />
                        </button>
                    @endfor
                </div>
            @endif

            <button
                type="button"
                data-card-control
                wire:click="mountAction('{{ $activities->isEmpty() && $canEdit ? 'scheduleKanbanActivity' : 'kanbanCardActivities' }}', { record: '{{ $recordId }}' })"
                class="flex items-center rounded-md p-0.5 hover:bg-gray-100 dark:hover:bg-white/10 {{ $activityColor }}"
                title="{{ $activityTitle }}"
                aria-label="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activities') }}"
            >
                <x-filament::icon :icon="$activityIcon($activities->first())" class="h-4 w-4" />
            </button>
        </div>

        @if ($colorHex || $status || $salesperson)
            <div class="flex items-center gap-1.5">
                @if ($colorHex)
                    <span class="crm-kanban-color-dot" style="background-color: {{ $colorHex }};" title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.color') }}"></span>
                @endif

                @if ($status)
                    <x-filament::badge size="sm" :color="$status === 'won' ? 'success' : 'danger'">
                        {{ $status === 'won' ? __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.won') : __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.lost') }}
                    </x-filament::badge>
                @endif

                @if ($salesperson)
                    <span
                        class="crm-kanban-avatar flex h-6 w-6 shrink-0 items-center justify-center overflow-hidden rounded-full text-[10px] font-semibold"
                        title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.salesperson') }}: {{ $salesperson->name }}"
                    >
                        @if ($avatar = $salesperson->avatar_url)
                            <img src="{{ $avatar }}" alt="{{ $salesperson->name }}" class="h-full w-full object-cover" />
                        @else
                            {{ $initials }}
                        @endif
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>
