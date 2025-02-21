<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource\Pages;
use Webkul\Inventory\Models\Packaging;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Filament\Resources\PackagingResource as BasePackagingResource;

class PackagingResource extends BasePackagingResource
{
    protected static ?string $model = Packaging::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(ProductSettings::class)->enable_packagings;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/packaging.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/packaging.navigation.title');
    }

    public static function form(Form $form): Form
    {
        $form = BasePackagingResource::form($form);

        $components = $form->getComponents();

        $components[] = Forms\Components\Select::make('package_type_id')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.form.package-type'))
            ->relationship('packageType', 'name')
            ->searchable()
            ->preload()
            ->visible(fn(OperationSettings $settings) => $settings->enable_packages);

        $components[] = Forms\Components\Select::make('routes')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.form.routes'))
            ->relationship('routes', 'name')
            ->searchable()
            ->preload()
            ->multiple()
            ->visible(fn(WarehouseSettings $settings) => $settings->enable_multi_steps_routes);

        $form->components($components);

        return $form;
    }

    public static function table(Table $table): Table
    {
        $table = BasePackagingResource::table($table);

        $columns = $table->getColumns();

        $filters = $table->getFilters();

        $columns[] = Tables\Columns\TextColumn::make('packageType.name')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.table.columns.package-type'))
            ->numeric()
            ->sortable()
            ->visible(fn(OperationSettings $settings) => $settings->enable_packages);

        $filters[] = Tables\Filters\SelectFilter::make('packageType')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.table.filters.package-type'))
            ->relationship('packageType', 'name')
            ->searchable()
            ->preload()
            ->visible(fn(OperationSettings $settings) => $settings->enable_packages);

        $table->columns($columns);

        $table->filters($filters);

        return $table;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $infolist = BasePackagingResource::infolist($infolist);

        $components = $infolist->getComponents();

        $firstSectionChildComponents = $components[0]->getChildComponents();

        $firstSectionChildComponents[] = Infolists\Components\TextEntry::make('packageType.name')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.general.entries.package_type'))
            ->icon('heroicon-o-archive-box')
            ->placeholder('—')
            ->visible(fn(OperationSettings $settings) => $settings->enable_packages);

        $components[0]->childComponents($firstSectionChildComponents);

        array_splice($components, 1, 0, [
            Infolists\Components\Section::make(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.routing.title'))
                ->schema([
                    Infolists\Components\RepeatableEntry::make('routes')
                        ->label(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.routing.entries.routes'))
                        ->schema([
                            Infolists\Components\TextEntry::make('name')
                                ->label(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.routing.entries.route_name'))
                                ->icon('heroicon-o-truck'),
                        ])
                        ->placeholder('—')
                        ->columns(1),
                ])
                ->collapsible()
                ->visible(fn(WarehouseSettings $settings) => $settings->enable_multi_steps_routes),
        ]);

        $infolist->components($components);

        return $infolist;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePackagings::route('/'),
        ];
    }
}
