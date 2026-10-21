@php
    $recordId = $record->getKey();
    $hasClickAction = $board->hasCardClickAction();
    $url = ! $hasClickAction ? $board->getRecordUrl($record) : null;
    $footerActions = $board->getCardFooterActions();
    $hasFooterActions = ! empty($footerActions);
@endphp

<div
    data-kanban-card
    data-record-id="{{ $recordId }}"
    @if($url)
        x-on:dblclick="window.location.href = '{{ $url }}'"
    @endif
    role="listitem"
    aria-label="{{ $board->resolveCardTitle($record) }}"
    @class([
        'fi-kanban-card rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-950/5 transition-shadow hover:shadow-md dark:bg-gray-900 dark:ring-white/10',
        'cursor-pointer' => $hasClickAction,
        'cursor-grab' => ! $hasClickAction,
    ])
>
    <div class="text-sm font-medium text-gray-950 dark:text-white">
        {{ $board->resolveCardTitle($record) }}
    </div>

    @if($description = $board->resolveCardDescription($record))
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ $description }}
        </p>
    @endif

    @if($badges = $board->resolveCardBadges($record))
        <div class="mt-2 flex flex-wrap gap-1">
            @foreach($badges as $badge)
                @php
                    $badgeColor = $badge['color'] ?? 'gray';
                    $badgeClasses = match($badgeColor) {
                        'primary' => 'bg-primary-50 text-primary-700 dark:bg-primary-400/10 dark:text-primary-400',
                        'success' => 'bg-success-50 text-success-700 dark:bg-success-400/10 dark:text-success-400',
                        'warning' => 'bg-warning-50 text-warning-700 dark:bg-warning-400/10 dark:text-warning-400',
                        'danger' => 'bg-danger-50 text-danger-700 dark:bg-danger-400/10 dark:text-danger-400',
                        'info' => 'bg-info-50 text-info-700 dark:bg-info-400/10 dark:text-info-400',
                        default => 'bg-gray-50 text-gray-700 dark:bg-gray-400/10 dark:text-gray-400',
                    };
                @endphp
                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-medium {{ $badgeClasses }}">
                    {{ $badge['label'] }}
                </span>
            @endforeach
        </div>
    @endif

    @if($hasFooterActions)
        <div class="mt-2 flex items-center justify-end gap-1 border-t border-gray-100 pt-2 dark:border-white/5">
            @foreach($footerActions as $footerAction)
                @php
                    $actionUrl = $footerAction->record($record)->getUrl();
                    $actionColorClasses = match($footerAction->getColor() ?? 'gray') {
                        'primary' => 'text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-400/10',
                        'danger' => 'text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-400/10',
                        'success' => 'text-success-500 hover:bg-success-50 dark:hover:bg-success-400/10',
                        'warning' => 'text-warning-500 hover:bg-warning-50 dark:hover:bg-warning-400/10',
                        default => 'text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10 dark:hover:text-gray-300',
                    };
                @endphp

                @if($actionUrl)
                    <a
                        href="{{ $actionUrl }}"
                        @if($footerAction->shouldOpenUrlInNewTab()) target="_blank" @endif
                        class="rounded-md p-1 transition {{ $actionColorClasses }}"
                        title="{{ $footerAction->getLabel() }}"
                        x-on:click.stop
                    >
                        @if($footerAction->getIcon())
                            <x-filament::icon :icon="$footerAction->getIcon()" class="h-4 w-4" />
                        @else
                            <span class="text-xs">{{ $footerAction->getLabel() }}</span>
                        @endif
                    </a>
                @else
                    <button
                        type="button"
                        wire:click.stop="mountAction('kanbanCardFooter', { record: '{{ $recordId }}', actionName: '{{ $footerAction->getName() }}' })"
                        class="rounded-md p-1 transition {{ $actionColorClasses }}"
                        title="{{ $footerAction->getLabel() }}"
                        aria-label="{{ $footerAction->getLabel() }}"
                    >
                        @if($footerAction->getIcon())
                            <x-filament::icon :icon="$footerAction->getIcon()" class="h-4 w-4" />
                        @else
                            <span class="text-xs">{{ $footerAction->getLabel() }}</span>
                        @endif
                    </button>
                @endif
            @endforeach
        </div>
    @endif

</div>
