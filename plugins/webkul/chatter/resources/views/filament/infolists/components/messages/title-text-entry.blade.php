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

        <div class="flex-1 pt-0.5 space-y-1">
            <div class="flex items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5">
                    <span class="text-base font-semibold dark:text-gray-100 text-gray-900 font-inter cursor-pointer">
                        {{ $getRecord()->causer?->name }}
                    </span>

                    <span class="text-sm text-gray-500 dark:text-gray-400 font-inter">
                        {{ $getRecord()->created_at->diffForHumans() }}
                    </span>
                </div>

                <x-filament::icon-button
                    wire:click="pinMessage({{ $getRecord()->id }})"
                    :icon="$getRecord()->pinned_at ? 'icon-un-pin' : 'icon-pin'"
                    color="success"
                    :tooltip="$getRecord()->pinned_at 
                        ? __('chatter::views/filament/infolists/components/messages/title-text-entry.unpin') 
                        : __('chatter::views/filament/infolists/components/messages/title-text-entry.pin')"
                    :label="$getRecord()->pinned_at 
                        ? __('chatter::views/filament/infolists/components/messages/title-text-entry.unpin') 
                        : __('chatter::views/filament/infolists/components/messages/title-text-entry.pin')"
                    class="!p-1.5"
                />
            </div>
        </div>
    </div>
</x-dynamic-component>
