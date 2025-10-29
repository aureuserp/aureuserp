@php
    $record = $getRecord();
    $changes = is_array($record->properties) ? $record->properties : [];
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class('flex flex-col gap-2') }}>
    @if ($record->body)
        <div class="text-sm leading-6 text-gray-700">
            {!! $record->body !!}
        </div>
    @endif

    <div class="rounded-xl p-3 shadow-sm ring-1 ring-gray-200 overflow-x-hidden bg-gray-50/80 dark:bg-gray-950 dark:ring-gray-800 space-y-1">
        <div class="grid grid-cols-3 gap-6">

            {{-- Activity Type --}}
            @if ($record->activityType?->name)
                <div class="flex flex-col gap-2.5 w-fit">
                    <span class="text-xs font-semibold tracking-wider text-gray-900 dark:text-gray-400">
                        @lang('chatter::views/filament/infolists/components/activities/content-text-entry.activity')
                    </span>

                    <div class="flex items-center gap-2 px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg font-medium text-sm">
                        {{ $record->activityType?->name }}
                    </div>
                </div>
            @endif

            {{-- Assigned To --}}
            @if ($record->assignedTo)
                <div class="flex flex-col gap-2.5 w-fit">
                    <span class="text-xs font-semibold tracking-wider text-gray-900 dark:text-gray-400">
                        @lang('chatter::views/filament/infolists/components/activities/content-text-entry.assigned-to')
                    </span>

                    <div class="flex items-center gap-2 px-2 py-1 bg-gradient-to-br from-gray-50 to-gray-200 dark:from-gray-800 dark:to-gray-900 rounded-lg">
                        <x-filament-panels::avatar.user
                            size="sm"
                            :user="$record->assignedTo"
                            class="flex-shrink-0 dark:ring-gray-700"
                        />

                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ $record->assignedTo->name }}
                        </span>
                    </div>
                </div>
            @endif

            {{-- Due Date --}}
            @if ($record->date_deadline)
                @php
                    $deadline = \Carbon\Carbon::parse($record->date_deadline);
                    $now = now();
                    $daysDifference = $now->diffInDays($deadline, false);
                    $roundedDays = ceil(abs($daysDifference));

                    $deadlineDescription = $deadline->isToday()
                        ? __('chatter::views/filament/infolists/components/activities/content-text-entry.today')
                        : ($deadline->isFuture()
                            ? ($roundedDays === 1
                                ? __('chatter::views/filament/infolists/components/activities/content-text-entry.tomorrow')
                                : __('chatter::views/filament/infolists/components/activities/content-text-entry.due-in-days', ['days' => $roundedDays]))
                            : ($roundedDays === 1
                                ? __('chatter::views/filament/infolists/components/activities/content-text-entry.one-day-overdue')
                                : __('chatter::views/filament/infolists/components/activities/content-text-entry.days-overdue', ['days' => $roundedDays]))
                        );

                    $textColor = $deadline->isToday()
                        ? 'text-yellow-700 dark:text-yellow-400'
                        : ($deadline->isPast()
                            ? 'text-red-700 dark:text-red-400'
                            : 'text-green-700 dark:text-green-400');
                @endphp

                <div class="flex flex-col gap-2.5">
                    <span class="text-xs font-semibold tracking-wider text-gray-900 dark:text-gray-400">
                        @lang('chatter::views/filament/infolists/components/activities/content-text-entry.due-date')
                    </span>

                    <div class="flex items-center gap-3">
                        <x-filament::icon-button
                            icon="heroicon-m-calendar"
                            color="success"
                            size="sm"
                        />
                        <span class="text-sm font-semibold {{ $textColor }}">
                            {{ $deadlineDescription }}
                        </span>
                    </div>
                </div>
            @endif

            {{-- Summary --}}
            @if ($record->summary)
                <div class="flex flex-col gap-2.5">
                    <span class="text-xs font-semibold tracking-wider text-gray-900 dark:text-gray-400">
                        @lang('chatter::views/filament/infolists/components/activities/content-text-entry.summary')
                    </span>

                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-relaxed">
                        {{ $record->summary }}
                    </span>
                </div>
            @endif

        </div>
    </div>
</div>
