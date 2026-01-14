<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\CreatePackage;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\EditPackage;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\ListPackages;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\ManageOperations;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\ManageProducts;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\ViewPackage;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\RelationManagers\ProductsRelationManager;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Settings\OperationSettings;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $cluster = Products::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(OperationSettings::class)->enable_packages;
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/package.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('inventories::filament/clusters/products/resources/package.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/products/resources/package.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/products/resources/package.form.sections.general.fields.name-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                        Group::make()
                            ->schema([
                                Select::make('package_type_id')
                                    ->label(__('inventories::filament/clusters/products/resources/package.form.sections.general.fields.package-type'))
                                    ->relationship('packageType', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema): Schema => PackageTypeResource::form($schema)),
                                DatePicker::make('pack_date')
                                    ->label(__('inventories::filament/clusters/products/resources/package.form.sections.general.fields.pack-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->default(today()),
                                Select::make('location_id')
                                    ->label(__('inventories::filament/clusters/products/resources/package.form.sections.general.fields.location'))
                                    ->relationship('location', 'full_name')
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('packageType.name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.columns.package-type'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('location.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.columns.location'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.columns.company'))
                    ->sortable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('packageType.name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.groups.package-type')),
                Tables\Grouping\Group::make('location.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.groups.location')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                SelectFilter::make('package_type_id')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.filters.package-type'))
                    ->relationship('packageType', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('location_id')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.filters.location'))
                    ->relationship('location', 'full_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->action(function (Package $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/products/resources/package.table.actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/products/resources/package.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/products/resources/package.table.actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/products/resources/package.table.actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print-without-content')
                        ->label(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.print-without-content.label'))
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $pdf = PDF::loadView('inventories::filament.clusters.products.packages.actions.print-without-content', [
                                'records' => $records,
                            ]);

                            $pdf->setPaper('a4', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Package-Barcode.pdf');
                        }),
                    BulkAction::make('print-with-content')
                        ->label(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.print-with-content.label'))
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $pdf = PDF::loadView('inventories::filament.clusters.products.packages.actions.print-with-content', [
                                'records' => $records,
                            ]);

                            $pdf->setPaper('a4', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Package-Barcode.pdf');
                        }),
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.delete.notification.success.body')),
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-cube')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('packageType.name')
                                            ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.entries.package-type'))
                                            ->icon('heroicon-o-rectangle-stack')
                                            ->placeholder('—'),

                                        TextEntry::make('pack_date')
                                            ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.entries.pack-date'))
                                            ->icon('heroicon-o-calendar')
                                            ->date(),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('location.full_name')
                                            ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.entries.location'))
                                            ->icon('heroicon-o-map-pin')
                                            ->placeholder('—'),

                                        TextEntry::make('company.name')
                                            ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/products/resources/package.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPackage::class,
            EditPackage::class,
            ManageProducts::class,
            ManageOperations::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListPackages::route('/'),
            'create'     => CreatePackage::route('/create'),
            'edit'       => EditPackage::route('/{record}/edit'),
            'view'       => ViewPackage::route('/{record}/view'),
            'products'   => ManageProducts::route('/{record}/products'),
            'operations' => ManageOperations::route('/{record}/operations'),
        ];
    }
}
