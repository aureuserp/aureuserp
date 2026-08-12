<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Manufacturing\Filament\Clusters\Products;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\BillOfMaterialOverview;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\CreateBillOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\EditBillOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\ListBillsOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\ViewBillOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Schemas\BillOfMaterialForm;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Schemas\BillOfMaterialInfolist;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Tables\BillsOfMaterialTable;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Settings\OperationSettings;

class BillsOfMaterialResource extends Resource
{
    protected static ?string $model = BillOfMaterial::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Products::class;

    protected static ?string $recordTitleAttribute = 'code';

    public static function getModelLabel(): string
    {
        return __('manufacturing::models/bill-of-material.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/products/resources/bill-of-material.navigation.title');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public static function form(Schema $schema): Schema
    {
        return BillOfMaterialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BillsOfMaterialTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BillOfMaterialInfolist::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListBillsOfMaterial::route('/'),
            'create'   => CreateBillOfMaterial::route('/create'),
            'overview' => BillOfMaterialOverview::route('/{record}/overview'),
            'view'     => ViewBillOfMaterial::route('/{record}'),
            'edit'     => EditBillOfMaterial::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewBillOfMaterial::class,
            EditBillOfMaterial::class,
            BillOfMaterialOverview::class,
        ]);
    }

    public static function normalizeProductVariantData(array $data): array
    {
        if (! empty($data['product_variant_id'])) {
            $data['product_id'] = $data['product_variant_id'];
        }

        unset($data['product_variant_id']);

        return $data;
    }

    public static function getOperationSettings(): OperationSettings
    {
        return settings(OperationSettings::class);
    }
}
