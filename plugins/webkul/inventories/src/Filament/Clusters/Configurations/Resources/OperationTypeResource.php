<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

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
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages\CreateOperationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages\EditOperationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages\ListOperationTypes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages\ViewOperationType;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;

class OperationTypeResource extends Resource
{
    protected static ?string $model = OperationType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.sections.general.fields.operator-type'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/configurations/resources/operation-type.form.sections.general.fields.operator-type-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                    ])->columnSpanFull(),

                Tabs::make()
                    ->tabs([
                        Tab::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.title'))
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Select::make('type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.operator-type'))
                                                    ->required()
                                                    ->options(Enums\OperationType::class)
                                                    ->default(Enums\OperationType::INCOMING->value)
                                                    ->native(true)
                                                    ->live()
                                                    ->selectablePlaceholder(false)
                                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                                        // Clear existing values
                                                        $set('print_label', null);

                                                        // Get the new default values based on current type
                                                        $type = $get('type');
                                                        $warehouseId = $get('warehouse_id');

                                                        // Set new source location
                                                        $sourceLocationId = match ($type) {
                                                            Enums\OperationType::INCOMING => Location::where('type', LocationType::SUPPLIER->value)->first()?->id,
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
                                                            Enums\OperationType::OUTGOING => Location::where('type', LocationType::CUSTOMER->value)->first()?->id,
                                                            Enums\OperationType::INTERNAL => Location::where('is_replenish', 1)
                                                                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                                ->first()?->id,
                                                            default => null,
                                                        };

                                                        // Set the new values
                                                        $set('source_location_id', $sourceLocationId);
                                                        $set('destination_location_id', $destinationLocationId);
                                                    }),
                                                TextInput::make('sequence_code')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.sequence-prefix'))
                                                    ->required()
                                                    ->maxLength(255),
                                                Toggle::make('print_label')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.generate-shipping-labels'))
                                                    ->inline(false)
                                                    ->visible(fn (Get $get): bool => in_array($get('type'), [Enums\OperationType::OUTGOING->value, Enums\OperationType::INTERNAL->value])),
                                                Select::make('warehouse_id')
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
                                                    ->default(function (Get $get) {
                                                        return Warehouse::first()?->id;
                                                    }),
                                                Radio::make('reservation_method')
                                                    ->required()
                                                    ->options(ReservationMethod::class)
                                                    ->default(ReservationMethod::AT_CONFIRM->value)
                                                    ->visible(fn (Get $get): bool => $get('type') != Enums\OperationType::INCOMING->value),
                                                Toggle::make('auto_show_reception_report')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.show-reception-report'))
                                                    ->inline(false)
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.show-reception-report-hint-tooltip'))
                                                    ->visible(fn (OperationSettings $settings, Get $get): bool => $settings->enable_reception_report && in_array($get('type'), [Enums\OperationType::INCOMING->value, Enums\OperationType::INTERNAL->value])),
                                            ]),

                                        Group::make()
                                            ->schema([
                                                Select::make('company_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.company'))
                                                    ->relationship('company', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->default(Auth::user()->default_company_id),
                                                Select::make('return_operation_type_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.return-type'))
                                                    ->relationship('returnOperationType', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->visible(fn (Get $get): bool => $get('type') != Enums\OperationType::DROPSHIP->value),
                                                Select::make('create_backorder')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.create-backorder'))
                                                    ->required()
                                                    ->options(CreateBackorder::class)
                                                    ->default(CreateBackorder::ASK->value),
                                                Select::make('move_type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.move-type'))
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fields.move-type-hint-tooltip'))
                                                    ->options(MoveType::class)
                                                    ->visible(fn (Get $get): bool => $get('type') == Enums\OperationType::INTERNAL->value),
                                            ]),
                                    ])
                                    ->columns(2),
                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.title'))
                                    ->schema([
                                        Toggle::make('use_create_lots')
                                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.create-new'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.create-new-hint-tooltip'))
                                            ->inline(false),
                                        Toggle::make('use_existing_lots')
                                            ->label(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.use-existing'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.lots.fields.use-existing-hint-tooltip'))
                                            ->inline(false),
                                    ])
                                    ->visible(fn (TraceabilitySettings $settings, Get $get): bool => $settings->enable_lots_serial_numbers && $get('type') != Enums\OperationType::DROPSHIP->value),
                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.form.tabs.general.fieldsets.locations.title'))
                                    ->schema([
                                        Select::make('source_location_id')
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
                                            ->default(function (Get $get) {
                                                $type = $get('type');

                                                $warehouseId = $get('warehouse_id');

                                                return match ($type) {
                                                    Enums\OperationType::INCOMING => Location::where('type', LocationType::SUPPLIER->value)->first()?->id,
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
                                        Select::make('destination_location_id')
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
                                            ->default(function (Get $get) {
                                                $type = $get('type');
                                                $warehouseId = $get('warehouse_id');

                                                return match ($type) {
                                                    Enums\OperationType::INCOMING => Location::where('is_replenish', 1)
                                                        ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                                                        ->first()?->id,
                                                    Enums\OperationType::OUTGOING => Location::where('type', LocationType::CUSTOMER->value)->first()?->id,
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
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.name'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.company'))
                    ->searchable(),
                TextColumn::make('warehouse.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.warehouse'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
                SelectFilter::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.filters.type'))
                    ->options(Enums\OperationType::class)
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('warehouse_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.filters.warehouse'))
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
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
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records, ForceDeleteBulkAction $action) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();

                                $action->cancel();
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
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.empty-actions.create.label'))
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-queue-list')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(2),
                            ]),

                        Tabs::make()
                            ->tabs([
                                Tab::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.title'))
                                    ->icon('heroicon-o-cog')
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.type'))
                                                    ->icon('heroicon-o-cog'),
                                                TextEntry::make('sequence_code')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.sequence_code'))
                                                    ->icon('heroicon-o-tag'),
                                                IconEntry::make('print_label')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.print_label'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-printer'),
                                                TextEntry::make('warehouse.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.warehouse'))
                                                    ->icon('heroicon-o-building-office'),
                                                TextEntry::make('reservation_method')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.reservation_method'))
                                                    ->icon('heroicon-o-clock'),
                                                IconEntry::make('auto_show_reception_report')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.auto_show_reception_report'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-document-text'),
                                            ])
                                            ->columns(2),

                                        Group::make()
                                            ->schema([
                                                TextEntry::make('company.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.company'))
                                                    ->icon('heroicon-o-building-office'),
                                                TextEntry::make('returnOperationType.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.return_operation_type'))
                                                    ->icon('heroicon-o-arrow-uturn-left'),
                                                TextEntry::make('create_backorder')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.create_backorder'))
                                                    ->icon('heroicon-o-archive-box'),
                                                TextEntry::make('move_type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.move_type'))
                                                    ->icon('heroicon-o-arrows-right-left'),
                                            ])
                                            ->columns(2),

                                        Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.lots.title'))
                                            ->schema([
                                                IconEntry::make('use_create_lots')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.lots.entries.use_create_lots'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-plus-circle'),
                                                IconEntry::make('use_existing_lots')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.lots.entries.use_existing_lots'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-archive-box'),
                                            ]),

                                        Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.locations.title'))
                                            ->schema([
                                                TextEntry::make('sourceLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.locations.entries.source_location'))
                                                    ->icon('heroicon-o-map-pin'),
                                                TextEntry::make('destinationLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.locations.entries.destination_location'))
                                                    ->icon('heroicon-o-map-pin'),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan('full'),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
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
            'index'  => ListOperationTypes::route('/'),
            'create' => CreateOperationType::route('/create'),
            'view'   => ViewOperationType::route('/{record}'),
            'edit'   => EditOperationType::route('/{record}/edit'),
        ];
    }
}
