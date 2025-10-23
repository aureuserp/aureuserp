<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div class="flex items-start gap-3">
        <x-filament-panels::avatar.user
            size="md"
            :user="$getRecord()->user"
            class="cursor-pointer"
        />

        <div class="flex-1 min-w-0 pt-1.5 space-y-2">
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="text-base font-semibold text-gray-900 dark:text-gray-100 font-inter cursor-pointer">
                        {{ $getRecord()->causer?->name }}
                    </span>

                    <span class="text-sm text-gray-500 dark:text-gray-400 font-inter">
                        {{ $getRecord()->created_at->diffForHumans() }}
                    </span>
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
