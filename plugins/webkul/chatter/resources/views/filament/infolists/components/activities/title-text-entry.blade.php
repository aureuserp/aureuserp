<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div class="flex items-start gap-x-3">
        <x-filament-panels::avatar.user
            size="md"
            :user="$getRecord()->user"
            class="cursor-pointer"
        />

        <div class="min-w-0 flex-1 space-y-2 pt-[6px]">
            <div class="flex items-center gap-x-2">
                <div class="min-w-0 flex items-center gap-x-2">
                    <div class="truncate text-sm font-medium cursor-pointer text-gray-900 dark:text-gray-100">
                        {{ $getRecord()->causer?->name }}
                    </div>

                    <div class="shrink-0 text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ $getRecord()->created_at->diffForHumans() }}
                    </div>
                </div>

                <div class="ml-auto shrink-0">
                    <x-filament-actions::group
                        size="md"
                        :tooltip="__('chatter::views/filament/infolists/components/activities/title-text-entry.more-action-tooltip')"
                        dropdown-placement="bottom-end"
                        :actions="[
                            ($this->markAsDoneAction)(['id' => $getRecord()->id]),
                            ($this->editActivity)(['id' => $getRecord()->id]),
                            ($this->cancelActivity)(['id' => $getRecord()->id]),
                        ]"
                        class="text-gray-600 dark:text-gray-300"
                    />
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
