<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts;

use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\Pages\CreateOrderTemplateProduct;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\Pages\EditOrderTemplateProduct;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\Pages\ListOrderTemplateProducts;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\Pages\ViewOrderTemplateProduct;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\Schemas\OrderTemplateProductForm;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\Tables\OrderTemplateProductsTable;
use Webkul\Sale\Models\OrderTemplateProduct;

class OrderTemplateProductResource extends Resource
{
    protected static ?string $model = OrderTemplateProduct::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/order-template.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/order-template.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sales::filament/clusters/configurations/resources/order-template.navigation.group');
    }

    protected static ?string $cluster = Configuration::class;

    public static function form(Schema $schema): Schema
    {
        return OrderTemplateProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderTemplateProductsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOrderTemplateProducts::route('/'),
            'create' => CreateOrderTemplateProduct::route('/create'),
            'view'   => ViewOrderTemplateProduct::route('/{record}'),
            'edit'   => EditOrderTemplateProduct::route('/{record}/edit'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('sort')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.sort'))
                            ->numeric(),
                        TextEntry::make('orderTemplate.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.order-template')),
                        TextEntry::make('company.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.company')),
                        TextEntry::make('product.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.product')),
                        TextEntry::make('uom.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.product-uom')),
                        TextEntry::make('display_type')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.display-type')),
                        TextEntry::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.name')),
                        TextEntry::make('quantity')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.quantity'))
                            ->numeric(),
                    ])->columns(2),
            ]);
    }
}
