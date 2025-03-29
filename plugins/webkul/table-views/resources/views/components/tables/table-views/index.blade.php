@props([
    'activeTableView',
    'isActiveTableViewModified',
    'favoriteViews' => [],
    'savedViews' => [],
    'presetViews' => [],
])

<div class="flex items-center justify-between p-4">
    <h4 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
        @lang('table-views::app.views.component.tables.table-views.title')
    </h4>

    <div class="flex gap-x-4">
        {{ $this->createTableViewAction }}
        
        {{ $this->resetTableViewAction }}
    </div>
</div>

@foreach ([
    __('table-views::app.views.component.tables.table-views.favorite-views') => $favoriteViews,
    __('table-views::app.views.component.tables.table-views.saved-views') => array_diff_key($savedViews, $favoriteViews),
    __('table-views::app.views.component.tables.table-views.preset-views') => array_diff_key($presetViews, $favoriteViews),
] as $label => $views)
    @if (! empty($views))
        <x-filament::dropdown.list>
            <x-filament::dropdown.header class="font-semibold">
                {{ $label }}
            </x-filament::dropdown.header>
            
            @foreach ($views as $key => $tableView)
                @php
                    $type = $tableView instanceof \Webkul\TableViews\Filament\Components\SavedView ? 'saved' : 'preset';
                @endphp
                
                <x-filament::dropdown.list.item
                    tag="a"
                    class="p-3"
                    :icon="$tableView->getIcon()"
                >
                    <div class="flex justify-between">
                        <div
                            class="w-full cursor-pointer select-none"
                            wire:click="mountAction('applyTableView', {{ json_encode([
                                'view_key' => $key, 
                                'view_type' => $type
                            ]) }})"
                        >
                            {{ $tableView->getLabel() }}
                        </div>

                        <div class="flex items-center gap-x-2">
                            <x-filament::icon
                                icon="heroicon-m-check"
                                @class([
                                    'text-primary-600 h-4 w-4',
                                    'visible' => $key == $activeTableView,
                                    'hidden' => $key != $activeTableView,
                                ])
                            />
                            
                            <x-filament::icon
                                :icon="$tableView->getVisibilityIcon()"
                                class="h-4 w-4 text-gray-400 dark:text-gray-200"
                            />
                            
                            {{ $this->getTableViewActionGroup($key, $type, $tableView) }}
                        </div>
                    </div>
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    @endif
@endforeach