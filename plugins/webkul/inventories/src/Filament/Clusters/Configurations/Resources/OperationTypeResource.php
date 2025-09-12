<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;

class OperationTypeResource extends Resource
{
    protected static ?string $model = OperationType::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/operation-type.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/operation-type.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/operation-type.model.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/operation-type.model.label-plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.sections.general.fields.operator-type'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/configurations/resources/operation-type.form.sections.general.fields.operator-type-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                    ]),

                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.title'))
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Select::make('type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.operator-type'))
                                                    ->required()
                                                    ->options(Enums\OperationType::class)
                                                    ->default(Enums\OperationType::INCOMING->value)
                                                    ->native(true)
                                                    ->live()
                                                    ->selectablePlaceholder(false)
                                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                                        // Clear existing values
                                                        $set('print_label', null);

                                                        // Get the new default values based on current type
                                                        $type = $get('type');
                                                        $warehouseId = $get('warehouse_id');

                                                        // Set new source location
                                                        $sourceLocationId = match ($type) {
                                                            Enums\OperationType::INCOMING => Location::where('type', Enums\LocationType::SUPPLIER->value)->first()?->id,
                                                            Enums\OperationType::OUTGOING => Location::where('is_replenish', 1)
                                                                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                                ->first()?->id,
                                                            Enums\OperationType::INTERNAL => Location::where('is_replenish', 1)
                                                                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                                ->first()?->id,
                                                            default => null,
                                                        };

                                                        // Set new destination location
                                                        $destinationLocationId = match ($type) {
                                                            Enums\OperationType::INCOMING => Location::where('is_replenish', 1)
                                                                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                                ->first()?->id,
                                                            Enums\OperationType::OUTGOING => Location::where('type', Enums\LocationType::CUSTOMER->value)->first()?->id,
                                                            Enums\OperationType::INTERNAL => Location::where('is_replenish', 1)
                                                                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                                ->first()?->id,
                                                            default => null,
                                                        };

                                                        // Set the new values
                                                        $set('source_location_id', $sourceLocationId);
                                                        $set('destination_location_id', $destinationLocationId);
                                                    }),
                                                Forms\Components\TextInput::make('sequence_code')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.sequence-prefix'))
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\Toggle::make('print_label')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.generate-shipping-labels'))
                                                    ->inline(false)
                                                    ->visible(fn (Forms\Get $get): bool => in_array($get('type'), [Enums\OperationType::OUTGOING->value, Enums\OperationType::INTERNAL->value])),
                                                Forms\Components\Select::make('warehouse_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.warehouse'))
                                                    ->relationship(
                                                        'warehouse',
                                                        'name',
                                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                                    )
                                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                                    })
                                                    ->disableOptionWhen(function ($label) {
                                                        return str_contains($label, ' (Deleted)');
                                                    })
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->default(function (Forms\Get $get) {
                                                        return Warehouse::first()?->id;
                                                    }),
                                                Forms\Components\Radio::make('reservation_method')
                                                    ->required()
                                                    ->options(Enums\ReservationMethod::class)
                                                    ->default(Enums\ReservationMethod::AT_CONFIRM->value)
                                                    ->visible(fn (Forms\Get $get): bool => $get('type') != Enums\OperationType::INCOMING->value),
                                                Forms\Components\Toggle::make('auto_show_reception_report')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.show-reception-report'))
                                                    ->inline(false)
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.show-reception-report-hint-tooltip'))
                                                    ->visible(fn (OperationSettings $settings, Forms\Get $get): bool => $settings->enable_reception_report && in_array($get('type'), [Enums\OperationType::INCOMING->value, Enums\OperationType::INTERNAL->value])),
                                            ]),

                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Select::make('company_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.company'))
                                                    ->relationship('company', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->default(Auth::user()->default_company_id),
                                                Forms\Components\Select::make('return_operation_type_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.return-type'))
                                                    ->relationship('returnOperationType', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->visible(fn (Forms\Get $get): bool => $get('type') != Enums\OperationType::DROPSHIP->value),
                                                Forms\Components\Select::make('create_backorder')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.create-backorder'))
                                                    ->required()
                                                    ->options(Enums\CreateBackorder::class)
                                                    ->default(Enums\CreateBackorder::ASK->value),
                                                Forms\Components\Select::make('move_type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.move-type'))
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.move-type-hint-tooltip'))
                                                    ->options(Enums\MoveType::class)
                                                    ->visible(fn (Forms\Get $get): bool => $get('type') == Enums\OperationType::INTERNAL->value),
                                            ]),
                                    ])
                                    ->columns(2),
                                Forms\Components\Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.title'))
                                    ->schema([
                                        Forms\Components\Toggle::make('use_create_lots')
                                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.create-new'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.create-new-hint-tooltip'))
                                            ->inline(false),
                                        Forms\Components\Toggle::make('use_existing_lots')
                                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.use-existing'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.use-existing-hint-tooltip'))
                                            ->inline(false),
                                    ])
                                    ->visible(fn (TraceabilitySettings $settings, Forms\Get $get): bool => $settings->enable_lots_serial_numbers && $get('type') != Enums\OperationType::DROPSHIP->value),
                                Forms\Components\Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.title'))
                                    ->schema([
                                        Forms\Components\Select::make('source_location_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.fields.source-location'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.fields.source-location-hint-tooltip'))
                                            ->relationship(
                                                'sourceLocation',
                                                'full_name',
                                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->full_name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->default(function (Forms\Get $get) {
                                                $type = $get('type');

                                                $warehouseId = $get('warehouse_id');

                                                return match ($type) {
                                                    Enums\OperationType::INCOMING => Location::where('type', Enums\LocationType::SUPPLIER->value)->first()?->id,
                                                    Enums\OperationType::OUTGOING => Location::where('is_replenish', 1)
                                                        ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                        ->first()?->id,
                                                    Enums\OperationType::INTERNAL => Location::where('is_replenish', 1)
                                                        ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                        ->first()?->id,
                                                    default => null,
                                                };
                                            })
                                            ->live(),
                                        Forms\Components\Select::make('destination_location_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.fields.destination-location'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.fields.destination-location-hint-tooltip'))
                                            ->relationship(
                                                'destinationLocation',
                                                'full_name',
                                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->full_name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->default(function (Forms\Get $get) {
                                                $type = $get('type');
                                                $warehouseId = $get('warehouse_id');

                                                return match ($type) {
                                                    Enums\OperationType::INCOMING => Location::where('is_replenish', 1)
                                                        ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                        ->first()?->id,
                                                    Enums\OperationType::OUTGOING => Location::where('type', Enums\LocationType::CUSTOMER->value)->first()?->id,
                                                    Enums\OperationType::INTERNAL => Location::where(function ($query) use ($warehouseId) {
                                                        $query->whereNull('warehouse_id')
                                                            ->when($warehouseId, fn ($q) => $q->orWhere('warehouse_id', $warehouseId));
                                                    })->first()?->id,
                                                    default => null,
                                                };
                                            }),
                                    ])
                                    ->visible(fn (WarehouseSettings $settings): bool => $settings->enable_locations),
                                // Forms\Components\Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.packages.title'))
                                //     ->schema([
                                //         Forms\Components\Toggle::make('show_entire_packs')
                                //             ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.packages.fields.show-entire-package'))
                                //             ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.packages.fields.show-entire-package-hint-tooltip'))
                                //             ->inline(false),
                                //     ])
                                //     ->visible(fn (OperationSettings $settings, Forms\Get $get): bool => $settings->enable_packages && $get('type') != Enums\OperationType::DROPSHIP->value),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.company'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.warehouse'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('warehouse.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.groups.warehouse'))
                    ->collapsible(),
                Tables\Grouping\Group::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.groups.type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.filters.type'))
                    ->options(Enums\OperationType::class)
                    ->searchable()
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.filters.warehouse'))
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.restore.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->action(function (OperationType $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.force-delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.force-delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.empty-actions.create.label'))
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.general.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-queue-list')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(2),
                            ]),

                        Infolists\Components\Tabs::make()
                            ->tabs([
                                Infolists\Components\Tabs\Tab::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.title'))
                                    ->icon('heroicon-o-cog')
                                    ->schema([
                                        Infolists\Components\Group::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.type'))
                                                    ->icon('heroicon-o-cog'),
                                                Infolists\Components\TextEntry::make('sequence_code')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.sequence_code'))
                                                    ->icon('heroicon-o-tag'),
                                                Infolists\Components\IconEntry::make('print_label')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.print_label'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-printer'),
                                                Infolists\Components\TextEntry::make('warehouse.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.warehouse'))
                                                    ->icon('heroicon-o-building-office'),
                                                Infolists\Components\TextEntry::make('reservation_method')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.reservation_method'))
                                                    ->icon('heroicon-o-clock'),
                                                Infolists\Components\IconEntry::make('auto_show_reception_report')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.auto_show_reception_report'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-document-text'),
                                            ])
                                            ->columns(2),

                                        Infolists\Components\Group::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('company.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.company'))
                                                    ->icon('heroicon-o-building-office'),
                                                Infolists\Components\TextEntry::make('returnOperationType.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.return_operation_type'))
                                                    ->icon('heroicon-o-arrow-uturn-left'),
                                                Infolists\Components\TextEntry::make('create_backorder')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.create_backorder'))
                                                    ->icon('heroicon-o-archive-box'),
                                                Infolists\Components\TextEntry::make('move_type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.move_type'))
                                                    ->icon('heroicon-o-arrows-right-left'),
                                            ])
                                            ->columns(2),

                                        Infolists\Components\Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.lots.title'))
                                            ->schema([
                                                Infolists\Components\IconEntry::make('use_create_lots')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.lots.entries.use_create_lots'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-plus-circle'),
                                                Infolists\Components\IconEntry::make('use_existing_lots')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.lots.entries.use_existing_lots'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-archive-box'),
                                            ]),

                                        Infolists\Components\Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.locations.title'))
                                            ->schema([
                                                Infolists\Components\TextEntry::make('sourceLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.locations.entries.source_location'))
                                                    ->icon('heroicon-o-map-pin'),
                                                Infolists\Components\TextEntry::make('destinationLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.locations.entries.destination_location'))
                                                    ->icon('heroicon-o-map-pin'),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan('full'),
                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.entries.last-updated'))
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
            'index'  => Pages\ListOperationTypes::route('/'),
            'create' => Pages\CreateOperationType::route('/create'),
            'view'   => Pages\ViewOperationType::route('/{record}'),
            'edit'   => Pages\EditOperationType::route('/{record}/edit'),
        ];
    }
}
