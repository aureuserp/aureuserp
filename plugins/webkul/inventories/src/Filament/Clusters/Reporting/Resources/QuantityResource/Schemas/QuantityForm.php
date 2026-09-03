<?php

namespace Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;

class QuantityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.product'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('is_storable', true)->whereNull('is_configurable'),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('lot_id', null);
                        $set('package_id', null);
                    }),
                Select::make('location_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.location'))
                    ->relationship(
                        name: 'location',
                        titleAttribute: 'full_name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', LocationType::INTERNAL),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn (): ?int => Warehouse::where('company_id', current_company_id())->first()?->lot_stock_location_id)
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('package_id', null);
                    })
                    ->visible(QuantityResource::getWarehouseSettings()->enable_locations),
                Select::make('lot_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.lot'))
                    ->relationship(
                        name: 'lot',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('product_id', $get('product_id')),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema): Schema => LotResource::form($schema))
                    ->createOptionAction(function (Action $action, Get $get) {
                        $action->mutateDataUsing(function (array $data) use ($get) {
                            $data['product_id'] = $get('product_id');

                            return $data;
                        });
                    })
                    ->visible(fn (TraceabilitySettings $settings) => $settings->enable_lots_serial_numbers),
                Select::make('package_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.package'))
                    ->relationship(
                        name: 'package',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->where('location_id', $get('location_id'))
                            ->orWhereNull('location_id'),
                    )
                    ->searchable()
                    ->reactive()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema): Schema => PackageResource::form($schema))
                    ->createOptionUsing(function (array $data, Get $get) {
                        $data['location_id'] = $get('location_id');

                        return PackageResource::getModel()::create($data)->getKey();
                    })
                    ->visible(fn (OperationSettings $settings) => $settings->enable_packages),
                TextInput::make('quantity')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.on-hand-qty'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required(),
            ])
            ->columns(1);
    }
}
