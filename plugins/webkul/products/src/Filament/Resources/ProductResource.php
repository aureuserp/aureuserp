<?php

namespace Webkul\Product\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource\Schemas\ProductForm;
use Webkul\Product\Filament\Resources\ProductResource\Schemas\ProductInfolist;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;
use Webkul\Product\Filament\Resources\ProductResource\Tables\ProductsTable;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;

class ProductResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Product::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'reference', 'barcode'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('products::filament/resources/product.global-search.reference') => $record->reference,
            __('products::filament/resources/product.global-search.barcode')   => $record->barcode,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $schema = ProductForm::configure($schema);

        $components = $schema->getComponents();

        $firstGroupChildComponents = $components[0]->getDefaultChildComponents();

        $firstGroupChildComponents[] = Section::make()
            ->visible(! empty($customFormFields = static::getCustomFormFields()))
            ->schema($customFormFields)
            ->columns(2);

        $components[0]->childComponents($firstGroupChildComponents);

        $schema->components($components);

        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        $schema = ProductInfolist::configure($schema);

        $components = $schema->getComponents();

        $firstGroupChildComponents = $components[0]->getDefaultChildComponents();

        $customInfolistEntries = static::getCustomInfolistEntries();

        if (! empty($customInfolistEntries)) {
            $firstGroupChildComponents[] = Section::make()
                ->schema($customInfolistEntries)
                ->columns(2);
        }

        $components[0]->childComponents($firstGroupChildComponents);

        $schema->components($components);

        return $schema;
    }

    public static function table(Table $table): Table
    {
        $table = ProductsTable::configure($table);

        return $table
            ->columns(static::mergeCustomTableColumns(array_values($table->getColumns())))
            ->filters(static::mergeCustomTableFilters(array_values($table->getFilters())));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(array_merge(['uom', 'uomPO'], ProductSchemaRegistry::eagerLoads()));
    }

    public static function getDefaultUomIdByProductType(ProductType|string|null $type): ?int
    {
        if (is_string($type)) {
            $type = ProductType::tryFrom($type);
        }

        if ($type === ProductType::SERVICE) {
            $hoursUomId = UOM::query()
                ->whereHas('category', fn (Builder $query) => $query->where('name', 'Working Time'))
                ->whereRaw('LOWER(name) = ?', ['hours'])
                ->orderBy('id')
                ->value('id');

            if ($hoursUomId) {
                return $hoursUomId;
            }
        }

        $categoryName = $type === ProductType::SERVICE ? 'Working Time' : 'Unit';

        return UOM::query()
            ->whereHas('category', fn (Builder $query) => $query->where('name', $categoryName))
            ->orderBy('id')
            ->value('id')
            ?? UOM::query()->orderBy('id')->value('id');
    }
}
