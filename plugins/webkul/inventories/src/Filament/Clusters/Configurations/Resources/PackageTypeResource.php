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
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages\CreatePackageType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages\EditPackageType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages\ListPackageTypes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages\ViewPackageType;
use Webkul\Inventory\Models\PackageType;
use Webkul\Inventory\Settings\OperationSettings;

class PackageTypeResource extends Resource
{
    protected static ?string $model = PackageType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/package-type.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/package-type.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(OperationSettings::class)->enable_packages;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),

                        Fieldset::make(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.title'))
                            ->schema([
                                TextInput::make('length')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.length'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                                TextInput::make('width')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.width'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                                TextInput::make('height')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.height'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                            ])
                            ->columns(3),
                        TextInput::make('base_weight')
                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.weight'))
                            ->required()
                            ->numeric()
                            ->default(0.0000)
                            ->minValue(0)
                            ->maxValue(99999999999),
                        TextInput::make('max_weight')
                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.max-weight'))
                            ->required()
                            ->numeric()
                            ->default(0.0000)
                            ->minValue(0)
                            ->maxValue(99999999999),
                        TextInput::make('barcode')
                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.barcode'))
                            ->maxLength(255),
                        Select::make('company_id')
                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.name'))
                    ->searchable(),
                TextColumn::make('height')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.height'))
                    ->sortable(),
                TextColumn::make('width')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.width'))
                    ->sortable(),
                TextColumn::make('length')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.length'))
                    ->sortable(),
                TextColumn::make('barcode')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.barcode'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/package-type.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/package-type.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/package-type.table.bulk-actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/package-type.table.bulk-actions.delete.notification.body')),
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
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-tag')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large),

                                Group::make([
                                    Section::make(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.fieldsets.size.title'))
                                        ->schema([
                                            Grid::make(3)
                                                ->schema([
                                                    TextEntry::make('length')
                                                        ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.fieldsets.size.entries.length'))
                                                        ->icon('heroicon-o-arrows-right-left')
                                                        ->numeric()
                                                        ->suffix(' cm'),

                                                    TextEntry::make('width')
                                                        ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.fieldsets.size.entries.width'))
                                                        ->icon('heroicon-o-arrows-up-down')
                                                        ->numeric()
                                                        ->suffix(' cm'),

                                                    TextEntry::make('height')
                                                        ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.fieldsets.size.entries.height'))
                                                        ->icon('heroicon-o-arrows-up-down')
                                                        ->numeric()
                                                        ->suffix(' cm'),
                                                ]),
                                        ])
                                        ->icon('heroicon-o-cube'),
                                ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('base_weight')
                                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.weight'))
                                            ->icon('heroicon-o-scale')
                                            ->numeric()
                                            ->suffix(' kg'),

                                        TextEntry::make('max_weight')
                                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.max-weight'))
                                            ->icon('heroicon-o-scale')
                                            ->numeric()
                                            ->suffix(' kg'),
                                    ]),

                                TextEntry::make('barcode')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.barcode'))
                                    ->icon('heroicon-o-bars-4')
                                    ->placeholder('—'),

                                TextEntry::make('company.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.company'))
                                    ->icon('heroicon-o-building-office'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPackageTypes::route('/'),
            'create' => CreatePackageType::route('/create'),
            'view'   => ViewPackageType::route('/{record}'),
            'edit'   => EditPackageType::route('/{record}/edit'),
        ];
    }
}
