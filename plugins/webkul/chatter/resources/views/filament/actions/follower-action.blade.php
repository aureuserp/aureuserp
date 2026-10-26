<div class="max-h-[400px] w-full overflow-y-auto text-gray-900 dark:text-gray-100">
    @forelse($record->followers as $follower)
        <div
            wire:key="non-follower-{{ $follower->partner->id }}"
            class="group flex items-center justify-between gap-2 rounded-lg px-2 py-1.5 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/60"
        >
            <div class="flex min-w-0 items-center gap-2">
                <x-filament-panels::avatar.user
                    size="sm"
                    :user="$follower->partner"
                />
                <div class="min-w-0 leading-4">
                    <h3 class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $follower->partner->name }}</h3>
                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $follower->partner->email }}</p>
                </div>
            </div>

            <x-filament::icon-button
                wire:click="removeFollower({{ $follower->partner->id }})"
                icon="heroicon-s-user-minus"
                color="danger"
                size="sm"
                :tooltip="trans('Remove Follower')"
            />
        </div>
    @empty
        <div class="flex flex-col items-center justify-center p-3 text-center text-gray-500 dark:text-gray-400">
            <x-filament::icon
                icon="heroicon-o-user-group"
                class="mb-2 h-6 w-6 text-gray-400 dark:text-gray-500"
            />
            <p class="text-sm">
                {{ __('No followers have been added yet.') }}
            </p>
        </div>
    @endforelse
</div>
