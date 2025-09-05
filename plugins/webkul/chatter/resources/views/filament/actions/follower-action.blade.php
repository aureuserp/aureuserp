<div class="max-h-[400px] w-full overflow-y-auto text-gray-900 dark:text-gray-100">
    @forelse($record->followers as $follower)
        <div
            wire:key="non-follower-{{ $follower->partner->id }}"
            class="group flex items-center justify-between rounded-lg p-2 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/60"
        >
            <div class="flex items-center gap-3">
                <x-filament-panels::avatar.user
                    size="md"
                    :user="$follower->partner"
                />
                <div>
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $follower->partner->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $follower->partner->email }}</p>
                </div>
            </div>

            <x-filament::icon-button
                wire:click="removeFollower({{ $follower->partner->id }})"
                icon="heroicon-s-user-minus"
                color="danger"
                :tooltip="trans('Remove Follower')"
            />
        </div>
    @empty
        <div class="flex flex-col items-center justify-center p-4 text-center text-gray-500 dark:text-gray-400">
            <x-filament::icon
                icon="heroicon-o-user-group"
                class="mb-3 h-8 w-8 text-gray-400 dark:text-gray-500"
            />
            <p class="text-sm">
                {{ __('No followers have been added yet.') }}
            </p>
        </div>
    @endforelse
</div>
