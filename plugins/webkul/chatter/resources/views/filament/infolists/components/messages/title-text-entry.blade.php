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

        <div class="flex-grow space-y-1 pt-[2px]">
            <div class="flex items-center justify-between gap-x-3">
                <div class="flex items-center gap-x-2.5">
                    <div class="text-sm font-medium text-gray-900 cursor-pointer dark:text-gray-100">
                        {{ $getRecord()->causer?->name }}
                    </div>

                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ $getRecord()->created_at->diffForHumans() }}
                    </div>

                    @if($getRecord()->pinned_at)
                        <span class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-2 py-0.5 text-[10px] font-semibold text-primary-700 ring-1 ring-primary-600/10 dark:bg-primary-900/30 dark:text-primary-300 dark:ring-primary-400/20">
                            <x-heroicon-m-bookmark class="h-3.5 w-3.5" />
                            {{ __('chatter::views/filament/infolists/components/messages/title-text-entry.pinned') }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center flex-shrink-0 gap-1">
                    <x-filament::icon-button
                        wire:click="pinMessage({{ $getRecord()->id }})"
                        :icon="$getRecord()->pinned_at ? 'heroicon-m-bookmark' : 'heroicon-m-bookmark'"
                        :color="$getRecord()->pinned_at ? 'primary' : 'gray'"
                        :tooltip="$getRecord()->pinned_at ? __('chatter::views/filament/infolists/components/messages/title-text-entry.unpin') : __('chatter::views/filament/infolists/components/messages/title-text-entry.pin')"
                        :label="$getRecord()->pinned_at ? __('chatter::views/filament/infolists/components/messages/title-text-entry.unpin') : __('chatter::views/filament/infolists/components/messages/title-text-entry.pin')"
                        class="!p-1.5"
                    />
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
