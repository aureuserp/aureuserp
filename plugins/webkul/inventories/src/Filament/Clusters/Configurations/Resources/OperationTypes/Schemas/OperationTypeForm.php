<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;

class OperationTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.sections.general.fields.operator-type'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/configurations/resources/operation-type.form.sections.general.fields.operator-type-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                    ]),

                Tabs::make()
                    ->tabs([
                        Tab::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.title'))
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Select::make('type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.operator-type'))
                                                    ->required()
                                                    ->options(Enums\OperationType::class)
                                                    ->default(Enums\OperationType::INCOMING->value)
                                                    ->native(true)
                                                    ->live()
                                                    ->selectablePlaceholder(false)
                                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                                        // Clear existing values
                                                        $set('print_label', null);

                                                        // Get the new default values based on current type
                                                        $type = $get('type');
                                                        $warehouseId = $get('warehouse_id');

                                                        // Set new source location
                                                        $sourceLocationId = match ($type) {
                                                            Enums\OperationType::INCOMING => Location::where('type', LocationType::SUPPLIER->value)->first()?->id,
                                                            Enums\OperationType::OUTGOING => Location::where('is_replenish', 1)
                                                                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                                ->first()?->id,
                                                            Enums\OperationType::INTERNAL => Location::where('is_replenish', 1)
                                                                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                                ->first()?->id,
                                                            default => null,
                                                        };

                                                        // Set new destination location
                                                        $destinationLocationId = match ($type) {
                                                            Enums\OperationType::INCOMING => Location::where('is_replenish', 1)
                                                                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                                ->first()?->id,
                                                            Enums\OperationType::OUTGOING => Location::where('type', LocationType::CUSTOMER->value)->first()?->id,
                                                            Enums\OperationType::INTERNAL => Location::where('is_replenish', 1)
                                                                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                                ->first()?->id,
                                                            default => null,
                                                        };

                                                        // Set the new values
                                                        $set('source_location_id', $sourceLocationId);
                                                        $set('destination_location_id', $destinationLocationId);
                                                    }),
                                                TextInput::make('sequence_code')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.sequence-prefix'))
                                                    ->required()
                                                    ->maxLength(255),
                                                Toggle::make('print_label')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.generate-shipping-labels'))
                                                    ->inline(false)
                                                    ->visible(fn (Get $get): bool => in_array($get('type'), [Enums\OperationType::OUTGOING->value, Enums\OperationType::INTERNAL->value])),
                                                Select::make('warehouse_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.warehouse'))
                                                    ->relationship(
                                                        'warehouse',
                                                        'name',
                                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                                    )
                                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                                    })
                                                    ->disableOptionWhen(function ($label) {
                                                        return str_contains($label, ' (Deleted)');
                                                    })
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->default(function (Get $get) {
                                                        return Warehouse::first()?->id;
                                                    }),
                                                Radio::make('reservation_method')
                                                    ->required()
                                                    ->options(ReservationMethod::class)
                                                    ->default(ReservationMethod::AT_CONFIRM->value)
                                                    ->visible(fn (Get $get): bool => $get('type') != Enums\OperationType::INCOMING->value),
                                                Toggle::make('auto_show_reception_report')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.show-reception-report'))
                                                    ->inline(false)
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.show-reception-report-hint-tooltip'))
                                                    ->visible(fn (OperationSettings $settings, Get $get): bool => $settings->enable_reception_report && in_array($get('type'), [Enums\OperationType::INCOMING->value, Enums\OperationType::INTERNAL->value])),
                                            ]),

                                        Group::make()
                                            ->schema([
                                                Select::make('company_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.company'))
                                                    ->relationship('company', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->default(Auth::user()->default_company_id),
                                                Select::make('return_operation_type_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.return-type'))
                                                    ->relationship('returnOperationType', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->visible(fn (Get $get): bool => $get('type') != Enums\OperationType::DROPSHIP->value),
                                                Select::make('create_backorder')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.create-backorder'))
                                                    ->required()
                                                    ->options(CreateBackorder::class)
                                                    ->default(CreateBackorder::ASK->value),
                                                Select::make('move_type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.move-type'))
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.move-type-hint-tooltip'))
                                                    ->options(MoveType::class)
                                                    ->visible(fn (Get $get): bool => $get('type') == Enums\OperationType::INTERNAL->value),
                                            ]),
                                    ])
                                    ->columns(2),
                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.title'))
                                    ->schema([
                                        Toggle::make('use_create_lots')
                                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.create-new'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.create-new-hint-tooltip'))
                                            ->inline(false),
                                        Toggle::make('use_existing_lots')
                                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.use-existing'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.use-existing-hint-tooltip'))
                                            ->inline(false),
                                    ])
                                    ->visible(fn (TraceabilitySettings $settings, Get $get): bool => $settings->enable_lots_serial_numbers && $get('type') != Enums\OperationType::DROPSHIP->value),
                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.title'))
                                    ->schema([
                                        Select::make('source_location_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.fields.source-location'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.fields.source-location-hint-tooltip'))
                                            ->relationship(
                                                'sourceLocation',
                                                'full_name',
                                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->full_name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->default(function (Get $get) {
                                                $type = $get('type');

                                                $warehouseId = $get('warehouse_id');

                                                return match ($type) {
                                                    Enums\OperationType::INCOMING => Location::where('type', LocationType::SUPPLIER->value)->first()?->id,
                                                    Enums\OperationType::OUTGOING => Location::where('is_replenish', 1)
                                                        ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                        ->first()?->id,
                                                    Enums\OperationType::INTERNAL => Location::where('is_replenish', 1)
                                                        ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                        ->first()?->id,
                                                    default => null,
                                                };
                                            })
                                            ->live(),
                                        Select::make('destination_location_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.fields.destination-location'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.fields.destination-location-hint-tooltip'))
                                            ->relationship(
                                                'destinationLocation',
                                                'full_name',
                                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->full_name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->default(function (Get $get) {
                                                $type = $get('type');
                                                $warehouseId = $get('warehouse_id');

                                                return match ($type) {
                                                    Enums\OperationType::INCOMING => Location::where('is_replenish', 1)
                                                        ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                        ->first()?->id,
                                                    Enums\OperationType::OUTGOING => Location::where('type', LocationType::CUSTOMER->value)->first()?->id,
                                                    Enums\OperationType::INTERNAL => Location::where(function ($query) use ($warehouseId) {
                                                        $query->whereNull('warehouse_id')
                                                            ->when($warehouseId, fn ($q) => $q->orWhere('warehouse_id', $warehouseId));
                                                    })->first()?->id,
                                                    default => null,
                                                };
                                            }),
                                    ])
                                    ->visible(fn (WarehouseSettings $settings): bool => $settings->enable_locations),
                                // Forms\Components\Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.packages.title'))
                                //     ->schema([
                                //         Forms\Components\Toggle::make('show_entire_packs')
                                //             ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.packages.fields.show-entire-package'))
                                //             ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.packages.fields.show-entire-package-hint-tooltip'))
                                //             ->inline(false),
                                //     ])
                                //     ->visible(fn (OperationSettings $settings, Forms\Get $get): bool => $settings->enable_packages && $get('type') != Enums\OperationType::DROPSHIP->value),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }
}
