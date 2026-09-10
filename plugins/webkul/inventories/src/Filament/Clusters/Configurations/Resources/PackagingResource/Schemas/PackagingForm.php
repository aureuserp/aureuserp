<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource;
use Webkul\Inventory\Models\PackageType;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\Route;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\PackagingResource as BasePackagingResource;

class PackagingForm
{
    public static function configure(Schema $schema): Schema
    {
        $schema = BasePackagingResource::form($schema);

        $components = $schema->getComponents();

        $components[2] = Select::make('product_id')
            ->label(__('products::filament/resources/packaging.form.product'))
            ->relationship(
                'product',
                'name',
                fn (Builder $query, Get $get) => $query
                    ->where('type', ProductType::GOODS)
                    ->whereNull('is_configurable')
                    ->where(owned_by_company($get('company_id'))),
            )
            ->required()
            ->searchable()
            ->preload();

        $components[] = Select::make('package_type_id')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.form.package-type'))
            ->relationship(
                'packageType',
                'name',
                fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
            )
            ->searchable()
            ->preload()
            ->visible(PackagingResource::getOperationSettings()->enable_packages);

        $components[] = Select::make('routes')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.form.routes'))
            ->relationship(
                'routes',
                'name',
                fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
            )
            ->searchable()
            ->preload()
            ->multiple()
            ->visible(PackagingResource::getWarehouseSettings()->enable_multi_steps_routes);

        foreach ($components as $index => $component) {
            if ($component instanceof Select && $component->getName() === 'company_id') {
                $components[$index] = $component->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                    'product_id'      => Product::class,
                    'package_type_id' => PackageType::class,
                    'routes'          => Route::class,
                ], $state));
            }
        }

        $schema->components($components);

        return $schema;
    }
}
