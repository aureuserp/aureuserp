<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource\Pages\ManagePackagings;
use Webkul\Inventory\Models\Packaging;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\PackagingResource as BasePackagingResource;

class PackagingResource extends BasePackagingResource
{
    protected static ?string $model = Packaging::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return static::getProductSettings()->enable_packagings;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/packaging.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/packaging.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        $schema = BasePackagingResource::form($schema);

        $components = $schema->getComponents();

        $components[2] = Select::make('product_id')
            ->label(__('products::filament/resources/packaging.form.product'))
            ->relationship(
                'product',
                'name',
                fn ($query) => $query->where('type', ProductType::GOODS)->whereNull('is_configurable'),
            )
            ->required()
            ->searchable()
            ->preload();

        $components[] = Select::make('package_type_id')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.form.package-type'))
            ->relationship('packageType', 'name')
            ->searchable()
            ->preload()
            ->visible(static::getOperationSettings()->enable_packages);

        $components[] = Select::make('routes')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.form.routes'))
            ->relationship('routes', 'name')
            ->searchable()
            ->preload()
            ->multiple()
            ->visible(static::getWarehouseSettings()->enable_multi_steps_routes);

        $schema->components($components);

        return $schema;
    }

    public static function table(Table $table): Table
    {
        $table = BasePackagingResource::table($table);

        $columns = $table->getColumns();

        $filters = $table->getFilters();

        $columns[] = TextColumn::make('packageType.name')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.table.columns.package-type'))
            ->numeric()
            ->sortable()
            ->visible(static::getOperationSettings()->enable_packages);

        $filters[] = SelectFilter::make('packageType')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.table.filters.package-type'))
            ->relationship('packageType', 'name')
            ->searchable()
            ->preload()
            ->visible(static::getOperationSettings()->enable_packages);

        $table->columns($columns);

        $table->filters($filters);

        return $table;
    }

    public static function infolist(Schema $schema): Schema
    {
        $schema = BasePackagingResource::infolist($schema);

        $components = $schema->getComponents();

        $firstSectionChildComponents = $components[0]->getDefaultChildComponents();

        $firstSectionChildComponents[] = TextEntry::make('packageType.name')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.general.entries.package_type'))
            ->icon('heroicon-o-archive-box')
            ->placeholder('—')
            ->visible(static::getOperationSettings()->enable_packages);

        $components[0]->childComponents($firstSectionChildComponents);

        array_splice($components, 1, 0, [
            Section::make(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.routing.title'))
                ->schema([
                    RepeatableEntry::make('routes')
                        ->label(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.routing.entries.routes'))
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.routing.entries.route_name'))
                                ->icon('heroicon-o-truck'),
                        ])
                        ->placeholder('—')
                        ->columns(1),
                ])
                ->collapsible()
                ->visible(static::getWarehouseSettings()->enable_multi_steps_routes),
        ]);

        $schema->components($components);

        return $schema;
    }

    static public function getOperationSettings(): OperationSettings
    {
        return once(fn () => app(OperationSettings::class));
    }

    static public function getProductSettings(): ProductSettings
    {
        return once(fn () => app(ProductSettings::class));
    }

    static public function getWarehouseSettings(): WarehouseSettings
    {
        return once(fn () => app(WarehouseSettings::class));
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePackagings::route('/'),
        ];
    }
}
