<div class="w-full h-full overflow-x-hidden">
    <!-- Toolbar -->
    <div class="sticky top-0 z-10 -mx-4 -mt-4 mb-4 bg-white/85 px-4 py-3 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:bg-gray-950/75">
        <div class="flex flex-col items-center">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <!-- Primary actions -->
                <div class="flex flex-wrap items-center gap-2">
                    @foreach (['messageAction', 'logAction', 'activityAction', 'fileAction', 'followerAction', 'filtersAction'] as $action)
                        @if ($this->{$action}->isVisible())
                            {{ $this->{$action} }}
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Filters (messages tab only) -->
            @if ($this->tab === 'messages')
                <div class="flex flex-col w-full gap-2 mt-1">
                    <div class="flex flex-wrap items-center w-full gap-2">
                        @if ($this->hasFilters())
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($this->getActiveFilters()) }} active</span>
                            <button type="button" wire:click="clearAllFilters" class="text-xs font-medium text-primary-600 hover:underline dark:text-primary-400">
                                Clear all
                            </button>
                        @endif
                    </div>

                    @if ($this->hasFilters())
                        <div class="flex flex-wrap items-center w-full gap-2" wire:key="filters-{{ $search }}-{{ $filterType }}-{{ (int) $pinnedOnly }}-{{ $dateRange ?? 'null' }}-{{ $sortBy }}">
                            @foreach($this->getActiveFilters() as $filter)
                                <button
                                    type="button"
                                    wire:click="removeFilter('{{ $filter['key'] }}')"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-white/80 px-2.5 py-1 text-xs font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-white dark:bg-gray-900/60 dark:text-gray-200 dark:ring-gray-800 dark:hover:bg-gray-900/70"
                                >
                                    <span class="truncate max-w-[14rem]">{{ $filter['label'] }}</span>
                                    <x-heroicon-m-x-mark class="h-3.5 w-3.5" />
                                </button>
                            @endforeach

                            <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">{{ $this->getFilteredCount() }} of {{ $this->getTotalCount() }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @php $hasActivities = $this->record->activities && $this->record->activities->isNotEmpty(); @endphp
    <div class="{{ $hasActivities ? 'space-y-6' : '' }}">
        @if ($hasActivities)
            <div wire:key="activities-{{ $this->refreshTick }}">
                {{ $this->activityInfolist }}
            </div>
        @endif

        <div wire:key="messages-{{ $this->refreshTick }}">
            {{ $this->chatInfolist }}
        </div>
    </div>

    <x-filament-actions::modals />
</div>
