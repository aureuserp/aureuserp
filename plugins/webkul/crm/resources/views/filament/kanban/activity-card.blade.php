@php
    use Webkul\Crm\Enums\ActivityDueStatus;
    use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
    use Webkul\Crm\Models\Lead;

    $recordId = $record->getKey();
    $url = $board->getRecordUrl($record);
    $label = fn (string $key): string => __('crm::filament/clusters/sales/resources/activity.'.$key);

    $lead = $record->lead;
    $status = $record->getDueStatus();
    $statusColor = match ($status) {
        ActivityDueStatus::Overdue => 'text-danger-600 dark:text-danger-400',
        ActivityDueStatus::Today   => 'text-warning-600 dark:text-warning-400',
        default                    => 'text-success-600 dark:text-success-400',
    };
    $typeIcon = str_starts_with((string) $record->activityType?->icon, 'heroicon-') ? $record->activityType->icon : 'heroicon-o-clock';
    $colorHex = Lead::COLORS[$lead?->color] ?? null;
    $canEdit = ActivityResource::canEdit($record);

    $assignee = $record->assignedTo;
    $initials = $assignee ? collect(explode(' ', $assignee->name))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') : null;
    $title = $record->summary ?: $record->activityType?->name;
@endphp

<div
    data-kanban-card
    data-record-id="{{ $recordId }}"
    role="listitem"
    aria-label="{{ $title }}"
    title="{{ __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.open', ['name' => $lead?->name ?? $title]) }}"
    @if ($url)
        x-on:click="if (! (typeof dragStarted !== 'undefined' && dragStarted) && ! $event.target.closest('[data-card-control]')) window.location.href = @js($url)"
    @endif
    @class([
        'fi-kanban-card group relative rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-950/5 transition-shadow hover:shadow-md dark:bg-gray-900 dark:ring-white/10',
        'cursor-pointer' => $url,
    ])
    @if ($colorHex) style="border-inline-start: 4px solid {{ $colorHex }};" @endif
>
    <div class="flex items-start gap-2">
        <x-filament::icon :icon="$typeIcon" @class(['mt-0.5 h-4 w-4 shrink-0', $statusColor]) title="{{ $label('table.columns.activity-type') }}: {{ $record->activityType?->name }}" />
        <h4 class="text-sm font-semibold text-gray-950 dark:text-white" title="{{ $label('table.columns.summary') }}: {{ $title }}">
            {{ $title }}
        </h4>
    </div>

    @if ($lead)
        <p class="mt-1 flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300" title="{{ $label('table.columns.opportunity') }}">
            <x-filament::icon icon="heroicon-m-funnel" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
            <span class="truncate">{{ $lead->name }}</span>
        </p>
    @endif

    @if ($customer = $lead?->partner?->name ?? $lead?->contact_name ?? $lead?->company_name)
        <p class="mt-1 flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-400" title="{{ $label('table.columns.customer') }}">
            <x-filament::icon icon="heroicon-m-building-office-2" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
            <span class="truncate">{{ $customer }}</span>
        </p>
    @endif

    <div class="mt-2.5 flex items-center justify-between">
        <span @class(['flex items-center gap-1 text-xs font-medium', $statusColor]) title="{{ $label('table.columns.due-date') }}">
            <x-filament::icon icon="heroicon-m-calendar-days" class="h-3.5 w-3.5" />
            {{ $status->getLabel() }}@if ($record->date_deadline && ! $record->date_deadline->isToday()) · {{ $record->date_deadline->translatedFormat('M j, Y') }}@endif
        </span>

        <div class="flex items-center gap-1.5">
            @if ($canEdit)
                <button
                    type="button"
                    data-card-control
                    wire:click="mountAction('kanbanCardFooter', { record: '{{ $recordId }}', actionName: 'done' })"
                    class="rounded-md p-1 text-gray-400 transition hover:bg-success-50 hover:text-success-600 dark:hover:bg-success-400/10"
                    title="{{ $label('actions.done.label') }}"
                    aria-label="{{ $label('actions.done.label') }}"
                >
                    <x-filament::icon icon="heroicon-o-check-circle" class="h-4 w-4" />
                </button>
            @endif

            @if ($assignee)
                <span
                    class="flex h-6 w-6 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary-100 text-[10px] font-semibold text-primary-700 dark:bg-primary-400/20 dark:text-primary-300"
                    title="{{ $label('table.columns.assigned-to') }}: {{ $assignee->name }}"
                >
                    @if ($avatar = $assignee->avatar_url)
                        <img src="{{ $avatar }}" alt="{{ $assignee->name }}" class="h-full w-full object-cover" />
                    @else
                        {{ $initials }}
                    @endif
                </span>
            @endif
        </div>
    </div>
</div>
