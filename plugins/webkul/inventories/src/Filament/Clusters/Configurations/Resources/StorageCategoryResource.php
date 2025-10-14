<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\AllowNewProduct;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\CreateStorageCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\EditStorageCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ListStorageCategories;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ManageCapacityByPackages;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ManageCapacityByProducts;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ManageLocations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ViewStorageCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\RelationManagers\CapacityByPackagesRelationManager;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\RelationManagers\CapacityByProductsRelationManager;
use Webkul\Inventory\Models\StorageCategory;
use Webkul\Inventory\Settings\WarehouseSettings;

class StorageCategoryResource extends Resource
{
    protected static ?string $model = StorageCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(WarehouseSettings::class)->enable_locations;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/storage-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/storage-category.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('inventories::filament/clusters/configurations/resources/storage-category.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/configurations/resources/storage-category.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('max_weight')
                            ->label(__('inventories::filament/clusters/configurations/resources/storage-category.form.sections.general.fields.max-weight'))
                            ->numeric()
                            ->default(0.0000)
                            ->minValue(0)
                            ->maxValue(99999999),
                        Select::make('allow_new_products')
                            ->label(__('inventories::filament/clusters/configurations/resources/storage-category.form.sections.general.fields.allow-new-products'))
                            ->options(AllowNewProduct::class)
                            ->required()
                            ->default(AllowNewProduct::MIXED),
                        Select::make('company_id')
                            ->label(__('inventories::filament/clusters/configurations/resources/storage-category.form.sections.general.fields.company'))
                            ->relationship(name: 'company', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->default(Auth::user()->default_company_id),
                    ])
                    ->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.name'))
                    ->searchable(),
                TextColumn::make('allow_new_products')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.allow-new-products'))
                    ->sortable(),
                TextColumn::make('max_weight')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.max-weight'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.company'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('allow_new_products')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.groups.allow-new-products'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/storage-category.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/storage-category.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/storage-category.table.bulk-actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/storage-category.table.bulk-actions.delete.notification.body')),
                    ),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-tag'), // Example icon for name
                                TextEntry::make('max_weight')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.general.entries.max-weight'))
                                    ->numeric()
                                    ->icon('heroicon-o-scale'), // Example icon for max weight
                                TextEntry::make('allow_new_products')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.general.entries.allow-new-products'))
                                    ->icon('heroicon-o-plus-circle'), // Example icon for allow new products
                                TextEntry::make('company.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.general.entries.company'))
                                    ->icon('heroicon-o-building-office'), // Example icon for company
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        $route = request()->route()?->getName() ?? session('current_route');

        if ($route && $route != 'livewire.update') {
            session(['current_route' => $route]);
        } else {
            $route = session('current_route');
        }

        if ($route === self::getRouteBaseName().'.index') {
            return SubNavigationPosition::Start;
        }

        return SubNavigationPosition::Top;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewStorageCategory::class,
            EditStorageCategory::class,
            ManageCapacityByPackages::class,
            ManageCapacityByProducts::class,
            ManageLocations::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Capacity By Packages', [
                CapacityByPackagesRelationManager::class,
            ])
                ->icon('heroicon-o-cube'),

            RelationGroup::make('Capacity By Products', [
                CapacityByProductsRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-document-check'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListStorageCategories::route('/'),
            'create'     => CreateStorageCategory::route('/create'),
            'view'       => ViewStorageCategory::route('/{record}'),
            'edit'       => EditStorageCategory::route('/{record}/edit'),
            'packages'   => ManageCapacityByPackages::route('/{record}/packages'),
            'products'   => ManageCapacityByProducts::route('/{record}/products'),
            'locations'  => ManageLocations::route('/{record}/locations'),
        ];
    }
}
