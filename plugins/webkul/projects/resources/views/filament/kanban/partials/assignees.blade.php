@php
    use Webkul\Project\Filament\Resources\TaskResource;
    use Webkul\Security\Filament\Resources\UserResource;
@endphp

<x-filament::dropdown placement="bottom-end" teleport width="xs">
    <x-slot name="trigger">
        <button type="button" class="flex items-center -space-x-1.5" title="{{ $task->users->pluck('name')->implode(', ') }}">
            @foreach ($task->users->take(2) as $member)
                @include('projects::filament.kanban.partials.avatar', ['member' => $member, 'size' => 'h-6 w-6', 'text' => 'text-[10px]', 'ring' => 'ring-2 ring-white dark:ring-gray-900'])
            @endforeach

            @if ($task->users->count() > 2)
                <span class="relative flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-[9px] font-semibold text-gray-600 ring-2 ring-white dark:bg-white/10 dark:text-gray-300 dark:ring-gray-900">
                    +{{ $task->users->count() - 2 }}
                </span>
            @endif
        </button>
    </x-slot>

    <div class="p-3">
        <p class="text-[10px] font-bold uppercase tracking-wide text-gray-400">
            {{ __('projects::filament/kanban.card.assignees') }}
        </p>

        <div class="mt-2 space-y-3">
            @foreach ($task->users as $user)
                <div class="flex items-start gap-2">
                    @include('projects::filament.kanban.partials.avatar', ['member' => $user, 'size' => 'h-8 w-8', 'text' => 'text-[10px]'])

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-950 dark:text-white">{{ $user->name }}</p>

                        @if ($user->email)
                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        @endif

                        @if (UserResource::canView($user))
                            <a
                                href="{{ UserResource::getUrl('view', ['record' => $user->getKey()]) }}"
                                class="mt-1 inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
                            >
                                <x-filament::icon icon="heroicon-m-user" class="h-3 w-3" />
                                {{ __('projects::filament/kanban.assign.view-profile') }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if (TaskResource::canEdit($task))
            <x-filament::button
                size="xs"
                color="gray"
                icon="heroicon-m-user-plus"
                wire:click="mountAction('assignTask', { record: '{{ $task->getKey() }}' })"
                class="mt-3 w-full"
            >
                {{ __('projects::filament/kanban.assign.save') }}
            </x-filament::button>
        @endif
    </div>
</x-filament::dropdown>
