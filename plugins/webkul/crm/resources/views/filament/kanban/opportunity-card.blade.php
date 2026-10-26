@php
    use Illuminate\Support\Number;
    use Webkul\Crm\Enums\ActivityDueStatus;
    use Webkul\Crm\Enums\OpportunityPriority;
    use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
    use Webkul\Crm\Models\Lead;

    $recordId = $record->getKey();
    $url = $board->getRecordUrl($record);
    $label = fn (string $key, array $replace = []): string => __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.'.$key, $replace);

    $canEdit = OpportunityResource::canEdit($record);
    $canDelete = OpportunityResource::canForceDelete($record);
    $colorHex = Lead::COLORS[$record->color] ?? null;
    $currency = $record->company?->currency?->code ?? 'USD';
    $priority = $record->priority?->value ?? 0;
    $maxPriority = OpportunityPriority::VeryHigh->value;

    $activityState = $record->activityState();
    $activities = $record->crmActivities->sortBy(fn ($activity) => $activity->date_deadline?->timestamp ?? PHP_INT_MAX)->values();
    $activityIcon = fn ($activity): string => str_starts_with((string) $activity?->activityType?->icon, 'heroicon-') ? $activity->activityType->icon : 'heroicon-o-clock';
    $activityColor = match ($activityState) {
        ActivityDueStatus::Overdue => 'text-danger-600 dark:text-danger-400',
        ActivityDueStatus::Today   => 'text-warning-600 dark:text-warning-400',
        ActivityDueStatus::Planned => 'text-success-600 dark:text-success-400',
        default                    => 'text-gray-400 dark:text-gray-500',
    };

    $salesperson = $record->user;
    $initials = $salesperson ? collect(explode(' ', $salesperson->name))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') : null;
@endphp

<div
    data-kanban-card
    data-record-id="{{ $recordId }}"
    role="listitem"
    aria-label="{{ $record->name }}"
    title="{{ $label('open', ['name' => $record->name]) }}"
    x-on:click="if (! (typeof dragStarted !== 'undefined' && dragStarted) && ! $event.target.closest('[data-card-control]')) window.location.href = @js($url)"
    class="fi-kanban-card group relative cursor-pointer rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-950/5 transition-shadow hover:shadow-md dark:bg-gray-900 dark:ring-white/10"
    @if ($colorHex) style="border-inline-start: 4px solid {{ $colorHex }};" @endif
>
    @if ($ribbon = $record->trashed() ? 'lost' : ($record->isWon() ? 'won' : null))
        <div class="pointer-events-none absolute end-0 top-0 h-16 w-16 overflow-hidden rounded-se-lg" aria-hidden="true">
            <div @class([
                'absolute -end-7 top-3 w-24 rotate-45 py-0.5 text-center text-[10px] font-bold uppercase tracking-wide text-white shadow-sm',
                'bg-success-600' => $ribbon === 'won',
                'bg-danger-600' => $ribbon === 'lost',
            ])>
                {{ $label($ribbon) }}
            </div>
        </div>
    @endif

    <div class="flex items-start justify-between gap-2">
        <h4 @class(['text-sm font-semibold text-gray-950 dark:text-white', 'pe-8' => $record->trashed() || $record->isWon()]) title="{{ $label('title') }}: {{ $record->name }}">
            {{ $record->name }}
        </h4>

        @if ($canEdit || $canDelete)
            <div data-card-control class="relative z-10 -me-1 -mt-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                <x-filament::dropdown placement="bottom-end" teleport width="xs">
                    <x-slot name="trigger">
                        <button type="button" class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10" title="{{ $label('menu') }}" aria-label="{{ $label('menu') }}">
                            <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="h-4 w-4" />
                        </button>
                    </x-slot>

                    <x-filament::dropdown.list>
                        @if ($canEdit)
                            <x-filament::dropdown.list.item icon="heroicon-m-pencil-square" tag="a" :href="OpportunityResource::getUrl('edit', ['record' => $record])">
                                {{ $label('edit') }}
                            </x-filament::dropdown.list.item>
                        @endif

                        @if ($canDelete)
                            <x-filament::dropdown.list.item icon="heroicon-m-trash" color="danger" wire:click="mountAction('deleteKanbanCard', { record: '{{ $recordId }}' })">
                                {{ $label('delete') }}
                            </x-filament::dropdown.list.item>
                        @endif
                    </x-filament::dropdown.list>

                    @if ($canEdit)
                        <x-filament::dropdown.list>
                            <div class="px-2 py-1">
                                <p class="mb-1.5 text-xs font-medium text-gray-500 dark:text-gray-400">{{ $label('color') }}</p>
                                <div class="grid grid-cols-6 gap-1.5">
                                    <button
                                        type="button"
                                        wire:click="setKanbanCardColor('{{ $recordId }}', 0)"
                                        x-on:click="close()"
                                        title="{{ $label('no-color') }}"
                                        class="flex h-6 w-6 items-center justify-center rounded-full border border-gray-300 bg-white dark:border-white/20 dark:bg-gray-800"
                                    >
                                        @if (! $colorHex)
                                            <x-filament::icon icon="heroicon-m-check" class="h-3.5 w-3.5 text-gray-600" />
                                        @endif
                                    </button>
                                    @foreach (Lead::COLORS as $colorKey => $hex)
                                        <button
                                            type="button"
                                            wire:click="setKanbanCardColor('{{ $recordId }}', {{ $colorKey }})"
                                            x-on:click="close()"
                                            title="{{ $label('color') }} {{ $colorKey }}"
                                            class="flex h-6 w-6 items-center justify-center rounded-full"
                                            style="background-color: {{ $hex }};"
                                        >
                                            @if ($record->color === $colorKey)
                                                <x-filament::icon icon="heroicon-m-check" class="h-3.5 w-3.5 text-white" />
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </x-filament::dropdown.list>
                    @endif
                </x-filament::dropdown>
            </div>
        @endif
    </div>

    @if ((float) $record->expected_revenue > 0)
        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300" title="{{ $label('expected-revenue') }}">
            {{ Number::currency((float) $record->expected_revenue, $currency) }}
            @if ((float) $record->recurring_revenue > 0)
                <span class="text-xs text-gray-500">+ {{ Number::currency((float) $record->recurring_revenue, $currency) }}{{ $record->recurringPlan ? ' '.$record->recurringPlan->name : '' }}</span>
            @endif
        </p>
    @endif

    @if ($customer = $record->partner?->name ?? $record->contact_name ?? $record->company_name)
        <p class="mt-1 flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-400" title="{{ $label('customer') }}">
            <x-filament::icon icon="heroicon-m-building-office-2" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
            <span class="truncate">{{ $customer }}</span>
        </p>
    @endif

    @if ($record->tags->isNotEmpty())
        <div class="mt-2 flex flex-wrap gap-1" title="{{ $label('tags') }}">
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
            <div data-card-control class="flex items-center" title="{{ $label('priority') }}: {{ $record->priority?->getLabel() }}">
                @for ($star = 1; $star <= $maxPriority; $star++)
                    <button
                        type="button"
                        @if ($canEdit) wire:click="setKanbanCardPriority('{{ $recordId }}', {{ $star }})" @else disabled @endif
                        title="{{ OpportunityPriority::from($star)->getLabel() }}"
                        aria-label="{{ $label('priority') }}: {{ OpportunityPriority::from($star)->getLabel() }}"
                        class="p-0.5 {{ $canEdit ? 'hover:scale-110' : 'cursor-default' }}"
                    >
                        <x-filament::icon :icon="$star <= $priority ? 'heroicon-s-star' : 'heroicon-o-star'" @class(['h-4 w-4', 'text-warning-400' => $star <= $priority, 'text-gray-300 dark:text-gray-600' => $star > $priority]) />
                    </button>
                @endfor
            </div>

            <div data-card-control>
                <x-filament::dropdown placement="bottom-start" teleport width="sm">
                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="flex items-center rounded-md p-0.5 hover:bg-gray-100 dark:hover:bg-white/10 {{ $activityColor }}"
                            title="{{ $activityState ? trans_choice('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activities-'.$activityState->value, $activities->count(), ['count' => $activities->count()]).($activities->first()?->activityType ? ' · '.$activities->first()->activityType->name : '') : $label('no-activities') }}"
                            aria-label="{{ $label('activities') }}"
                        >
                            <x-filament::icon :icon="$activityIcon($activities->first())" class="h-4 w-4" />
                        </button>
                    </x-slot>

                    <div class="p-2">
                        <p class="mb-1 px-1 text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $label('activities') }}</p>

                        @forelse ($activities as $activity)
                            @php
                                $status = $activity->getDueStatus();
                            @endphp
                            <div class="flex items-start gap-2 rounded-md px-1 py-1.5">
                                <x-filament::icon
                                    :icon="$activityIcon($activity)"
                                    @class([
                                        'mt-0.5 h-4 w-4 shrink-0',
                                        'text-danger-600 dark:text-danger-400' => $status === ActivityDueStatus::Overdue,
                                        'text-warning-600 dark:text-warning-400' => $status === ActivityDueStatus::Today,
                                        'text-success-600 dark:text-success-400' => $status === ActivityDueStatus::Planned,
                                    ])
                                />
                                <div class="min-w-0 text-sm">
                                    <p class="font-medium text-gray-950 dark:text-white">{{ $activity->activityType?->name }}@if ($activity->summary): {{ $activity->summary }}@endif</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $status->getLabel() }}@if ($activity->date_deadline) · {{ $activity->date_deadline->isToday() ? $label('today') : $activity->date_deadline->diffForHumans(['parts' => 1]) }}@endif
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="px-1 py-2 text-xs italic text-gray-500 dark:text-gray-400">{{ $label('no-activities') }}</p>
                        @endforelse

                        @if ($canEdit)
                            <button
                                type="button"
                                wire:click="mountAction('scheduleKanbanActivity', { record: '{{ $recordId }}' })"
                                x-on:click="close()"
                                class="mt-1 flex w-full items-center justify-center gap-1 rounded-md bg-gray-100 px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10"
                            >
                                <x-filament::icon icon="heroicon-m-plus" class="h-4 w-4" />
                                {{ $label('schedule-activity') }}
                            </button>
                        @endif
                    </div>
                </x-filament::dropdown>
            </div>
        </div>

        @if ($salesperson)
            <span
                class="flex h-6 w-6 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary-100 text-[10px] font-semibold text-primary-700 dark:bg-primary-400/20 dark:text-primary-300"
                title="{{ $label('salesperson') }}: {{ $salesperson->name }}"
            >
                @if ($avatar = $salesperson->avatar_url)
                    <img src="{{ $avatar }}" alt="{{ $salesperson->name }}" class="h-full w-full object-cover" />
                @else
                    {{ $initials }}
                @endif
            </span>
        @endif
    </div>
</div>
