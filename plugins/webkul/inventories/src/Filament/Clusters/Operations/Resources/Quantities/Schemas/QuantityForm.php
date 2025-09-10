<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Quantities\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\PackageResource;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;

class QuantityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('location_id')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.location'))
                    ->relationship(
                        name: 'location',
                        titleAttribute: 'full_name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', LocationType::INTERNAL),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                Select::make('product_id')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.product'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('is_storable', true)->whereNull('is_configurable'),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $set('lot_id', null);
                    }),
                Select::make('lot_id')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.lot'))
                    ->relationship(
                        name: 'lot',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('product_id', $get('product_id')),
                    )
                    ->searchable()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema): Schema => LotResource::form($schema))
                    ->createOptionAction(function (Action $action, Get $get) {
                        $action
                            ->mutateDataUsing(function (array $data) use ($get): array {
                                $data['product_id'] = $get('product_id');

                                return $data;
                            });
                    })
                    ->visible(function (TraceabilitySettings $settings, Get $get): bool {
                        if (! $settings->enable_lots_serial_numbers) {
                            return false;
                        }

                        $product = Product::find($get('product_id'));

                        if (! $product) {
                            return false;
                        }

                        return $product->tracking === ProductTracking::LOT;
                    }),
                Select::make('package_id')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.package'))
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema): Schema => PackageResource::form($schema))
                    ->visible(fn (OperationSettings $settings) => $settings->enable_packages),
                TextInput::make('counted_quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.counted-qty'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required(),
                DatePicker::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.scheduled-at'))
                    ->native(false)
                    ->default(now()->setDay(app(OperationSettings::class)->annual_inventory_day)->setMonth(app(OperationSettings::class)->annual_inventory_month)),
            ])
            ->columns(1);
    }
}
