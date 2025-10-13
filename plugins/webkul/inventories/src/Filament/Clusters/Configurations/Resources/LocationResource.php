<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages\CreateLocation;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages\EditLocation;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages\ListLocations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages\ViewLocation;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ManageLocations;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Settings\WarehouseSettings;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'full_name';

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
        return __('inventories::filament/clusters/configurations/resources/location.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/location.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/location.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.general.fields.location'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('inventories::filament/clusters/configurations/resources/location.form.sections.general.fields.location-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                Select::make('parent_id')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.general.fields.parent-location'))
                                    ->relationship('parent', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.general.fields.parent-location-hint-tooltip')),

                                RichEditor::make('description')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.general.fields.external-notes')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.title'))
                            ->schema([
                                Select::make('type')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.location-type'))
                                    ->options(LocationType::class)
                                    ->selectablePlaceholder(false)
                                    ->required()
                                    ->default(LocationType::INTERNAL->value)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        if (! $get('type') === in_array($get('type'), [LocationType::INTERNAL->value, LocationType::INVENTORY->value])) {
                                            $set('is_scrap', false);
                                        }

                                        if ($get('type') !== LocationType::INTERNAL->value) {
                                            $set('storage_category_id', null);

                                            $set('is_replenish', false);
                                        }
                                    }),
                                Select::make('company_id')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::user()->default_company_id),
                                Select::make('storage_category_id')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.storage-category'))
                                    ->relationship('storageCategory', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema): Schema => StorageCategoryResource::form($schema))
                                    ->visible(fn (Get $get): bool => $get('type') === LocationType::INTERNAL->value)
                                    ->hiddenOn(ManageLocations::class),
                                Toggle::make('is_scrap')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-scrap'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-scrap-hint-tooltip'))
                                    ->visible(fn (Get $get): bool => in_array($get('type'), [LocationType::INTERNAL->value, LocationType::INVENTORY->value]))
                                    ->live(),

                                Toggle::make('is_dock')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-dock'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-dock-hint-tooltip')),

                                Toggle::make('is_replenish')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-replenish'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.is-replenish-hint-tooltip'))
                                    ->visible(fn (Get $get): bool => $get('type') === LocationType::INTERNAL->value),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.cyclic-counting'))
                                    ->schema([
                                        TextInput::make('cyclic_inventory_frequency')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.inventory-frequency'))
                                            ->integer()
                                            ->default(0),
                                        Placeholder::make('last_inventory_date')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.last-inventory'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.last-inventory-hint-tooltip'))
                                            ->content(fn ($record) => $record?->last_inventory_date?->toFormattedDateString() ?? '—'),
                                        Placeholder::make('next_inventory_date')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.next-expected'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/location.form.sections.settings.fields.next-expected-hint-tooltip'))
                                            ->content(fn ($record) => $record?->next_inventory_date?->toFormattedDateString() ?? '—'),
                                    ])
                                    ->visible(fn (Get $get): bool => in_array($get('type'), [LocationType::INTERNAL->value, LocationType::TRANSIT->value]))
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.location'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.type'))
                    ->searchable(),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.storage-category'))
                    ->placeholder('—')
                    ->sortable()
                    ->hiddenOn(ManageLocations::class),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.company'))
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('warehouse.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.groups.warehouse'))
                    ->collapsible(),
                Tables\Grouping\Group::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.groups.type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.filters.type'))
                    ->options(LocationType::class)
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('storage_category_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.filters.location'))
                    ->relationship('storageCategory', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->modalWidth('6xl'),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->modalWidth('6xl')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/location.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/location.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/location.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/location.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/location.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/location.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (ForceDeleteAction $action, Location $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/configurations/resources/location.table.actions.force-delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/location.table.actions.force-delete.notification.error.body'))
                                ->send();
                            $action->cancel();

                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/location.table.actions.force-delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/location.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print')
                        ->label(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.print.label'))
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $pdf = PDF::loadView('inventories::filament.clusters.configurations.locations.actions.print', [
                                'records' => $records,
                            ]);

                            $pdf->setPaper('a4', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Location-Barcode.pdf');
                        }),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (ForceDeleteBulkAction $action, Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                                $action->cancel();

                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
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
                        Section::make(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.general.entries.location'))
                                    ->icon('heroicon-o-map-pin')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(2),
                                TextEntry::make('parent.full_name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.general.entries.parent-location'))
                                    ->icon('heroicon-o-building-office-2'),
                                TextEntry::make('description')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.general.entries.external-notes'))
                                    ->markdown()
                                    ->placeholder('—'),
                                TextEntry::make('type')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.location-type'))
                                    ->icon('heroicon-o-tag'),
                                TextEntry::make('company.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.company'))
                                    ->icon('heroicon-o-building-office'),
                                TextEntry::make('storageCategory.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.storage-category'))
                                    ->icon('heroicon-o-archive-box')
                                    ->placeholder('—'),
                            ])
                            ->columns(2),

                        Section::make(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.title'))
                            ->schema([
                                IconEntry::make('is_scrap')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.is-scrap')),
                                IconEntry::make('is_dock')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.is-dock')),
                                IconEntry::make('is_replenish')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.is-replenish')),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.cyclic-counting'))
                                    ->schema([
                                        TextEntry::make('cyclic_inventory_frequency')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.inventory-frequency'))
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—'),
                                        TextEntry::make('cyclic_inventory_last')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.last-inventory'))
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—'),
                                        TextEntry::make('cyclic_inventory_next_expected')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.next-expected'))
                                            ->icon('heroicon-o-calendar-days')
                                            ->placeholder('—'),
                                    ]),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.record-information.entries.last-updated'))
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
            ViewLocation::class,
            EditLocation::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'view'   => ViewLocation::route('/{record}'),
            'edit'   => EditLocation::route('/{record}/edit'),
        ];
    }
}
