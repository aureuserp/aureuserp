@use('Illuminate\Support\Js')

@if (method_exists($this, 'getCachedFavoriteTableViews') && count($tabs = $this->getCachedFavoriteTableViews()))
    @php
        $tableViewsTriggerAction = $this->getTableViewsTriggerAction();
        $activeTableView = $this->getActiveTableView();
        $isActiveTableViewModified = $this->isActiveTableViewModified();
        $tableViewsFormMaxHeight = $this->getPresetTableViewsFormMaxHeight();
        $tableViewsFormWidth = $this->getPresetTableViewsFormWidth();

        $tableFavoriteViews = $this->getFavoriteTableViews();
        $tablePresetViews = $this->getPresetTableViews();
        $tableSavedViews = $this->getCachedTableViews();
    @endphp

    <div class="fi-table-views">
        <x-filament::tabs wire:listen="filtered-list-updated">
            @foreach ($tabs as $tabKey => $tab)
                @php
                    $tabKey = strval($tabKey);
                @endphp

                <x-table-views::tabs.item
                    class="whitespace-nowrap"
                    :active="$activeTableView === $tabKey"
                    :alpine-active="'$wire.activeTableView === \'' . $tabKey . '\''"
                    :badge="$tab->getBadge()"
                    :badge-color="$tab->getBadgeColor()"
                    :badge-icon="$tab->getBadgeIcon()"
                    :badge-icon-position="$tab->getBadgeIconPosition()"
                    :icon="$tab->getIcon()"
                    :icon-position="$tab->getIconPosition()"
                    x-on:click.capture="$wire.resetTableSearch(); $wire.loadView({{ Js::from(filled($tabKey) ? $tabKey : null) }})"
                    :attributes="$tab->getExtraAttributeBag()"
                >
                    {{ $tab->getLabel() ?? $this->generateTabLabel($tabKey) }}
                </x-table-views::tabs.item>
            @endforeach
        </x-filament::tabs>

        <div class="fi-table-views-actions">
            {{ $this->createTableViewAction }}

            <x-filament::dropdown
                :width="$tableViewsFormWidth"
                :max-height="$tableViewsFormMaxHeight"
                placement="bottom-end"
                shift
                wire:key="{{ $this->getId() }}.table.views"
            >
                <x-slot name="trigger">
                    {{ $tableViewsTriggerAction }}
                </x-slot>

                <x-table-views::tables.table-views
                    :active-table-view="$activeTableView"
                    :is-active-table-view-modified="$isActiveTableViewModified"
                    :favorite-views="$tableFavoriteViews"
                    :preset-views="$tablePresetViews"
                    :saved-views="$tableSavedViews"
                    class="p-0"
                />
            </x-filament::dropdown>
        </div>
    </div>
@endif
