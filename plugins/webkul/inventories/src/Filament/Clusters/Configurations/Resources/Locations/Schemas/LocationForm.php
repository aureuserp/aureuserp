<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Locations\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategories\Pages\ManageLocations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategories\StorageCategoryResource;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/location.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.general.fields.location'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('inventories::filament/clusters/configurations/resources/location.form.sections.general.fields.location-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                Select::make('parent_id')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.general.fields.parent-location'))
                                    ->relationship('parent', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.general.fields.parent-location-hint-tooltip')),

                                RichEditor::make('description')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.general.fields.external-notes')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.title'))
                            ->schema([
                                Select::make('type')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.location-type'))
                                    ->options(LocationType::class)
                                    ->selectablePlaceholder(false)
                                    ->required()
                                    ->default(LocationType::INTERNAL->value)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        if (! $get('type') === in_array($get('type'), [LocationType::INTERNAL->value, LocationType::INVENTORY->value])) {
                                            $set('is_scrap', false);
                                        }

                                        if ($get('type') !== LocationType::INTERNAL->value) {
                                            $set('storage_category_id', null);

                                            $set('is_replenish', false);
                                        }
                                    }),
                                Select::make('company_id')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::user()->default_company_id),
                                Select::make('storage_category_id')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.storage-category'))
                                    ->relationship('storageCategory', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema): Schema => StorageCategoryResource::form($schema))
                                    ->visible(fn (Get $get): bool => $get('type') === LocationType::INTERNAL->value)
                                    ->hiddenOn(ManageLocations::class),
                                Toggle::make('is_scrap')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-scrap'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-scrap-hint-tooltip'))
                                    ->visible(fn (Get $get): bool => in_array($get('type'), [LocationType::INTERNAL->value, LocationType::INVENTORY->value]))
                                    ->live(),

                                Toggle::make('is_dock')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-dock'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-dock-hint-tooltip')),

                                Toggle::make('is_replenish')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-replenish'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-replenish-hint-tooltip'))
                                    ->visible(fn (Get $get): bool => $get('type') === LocationType::INTERNAL->value),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.cyclic-counting'))
                                    ->schema([
                                        TextInput::make('cyclic_inventory_frequency')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.inventory-frequency'))
                                            ->integer()
                                            ->default(0),
                                        Placeholder::make('last_inventory_date')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.last-inventory'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.last-inventory-hint-tooltip'))
                                            ->content(fn ($record) => $record?->last_inventory_date?->toFormattedDateString() ?? '—'),
                                        Placeholder::make('next_inventory_date')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.next-expected'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.next-expected-hint-tooltip'))
                                            ->content(fn ($record) => $record?->next_inventory_date?->toFormattedDateString() ?? '—'),
                                    ])
                                    ->visible(fn (Get $get): bool => in_array($get('type'), [LocationType::INTERNAL->value, LocationType::TRANSIT->value]))
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
