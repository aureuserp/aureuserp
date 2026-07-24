<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources;

use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Enums\WorkCenterWorkingState;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Manufacturing\Filament\Clusters\Operations;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Pages\EditWorkOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Pages\ListWorkOrders;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Pages\ViewWorkOrder;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\Product;
use Webkul\Manufacturing\Models\WorkCenterProductivityLog;
use Webkul\Manufacturing\Models\WorkCenterProductivityLoss;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Manufacturing\Settings\OperationSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;
use Webkul\Support\Models\UOM;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;

    protected static ?string $cluster = Operations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(OperationSettings::class)->enable_work_orders;
    }

    public static function getModelLabel(): string
    {
        return __('manufacturing::models/work-order.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/work-order.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/work-order.navigation.group');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(fn (?WorkOrder $record): array => static::getVisibleWorkOrderStateOptions($record?->state?->value ?? $record?->state))
                    ->default(WorkOrderState::PENDING)
                    ->disabled()
                    ->dehydrated(),

                Section::make(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.title'))
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->columns(1)
                            ->schema([
                                Select::make('operation_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.work-order'))
                                    ->relationship('operation', 'name', fn (Builder $query) => $query->withTrashed())
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->createOptionForm(fn (Schema $schema): Schema => OperationResource::form($schema->model(Operation::class)))
                                    ->createOptionAction(fn (Action $action) => $action->modalWidth(Width::SevenExtraLarge))
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                        $operation = Operation::query()->withTrashed()->find($state);

                                        $set('work_center_id', $operation?->work_center_id);
                                        $set('name', $operation?->name);

                                        static::syncExpectedDuration($set, $get, $operation);
                                    })
                                    ->required()
                                    ->disabled(fn (?WorkOrder $record): bool => $record && ! in_array($record->state, [WorkOrderState::PENDING, WorkOrderState::WAITING], true)),
                                Select::make('work_center_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.work-center'))
                                    ->relationship('workCenter', 'name', fn (Builder $query) => $query->withTrashed())
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->required()
                                    ->disabled(fn (?WorkOrder $record): bool => $record && ! in_array($record->state, [WorkOrderState::PENDING, WorkOrderState::WAITING], true)),
                                Group::make()
                                    ->columns(2)
                                    ->schema([
                                        DateTimePicker::make('started_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.start-date'))
                                            ->native(false)
                                            ->seconds(false)
                                            ->disabled(fn (?WorkOrder $record): bool => $record && in_array($record->state, [WorkOrderState::DONE, WorkOrderState::CANCEL], true)),
                                        DateTimePicker::make('finished_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.end-date'))
                                            ->native(false)
                                            ->seconds(false)
                                            ->disabled(fn (?WorkOrder $record): bool => $record && in_array($record->state, [WorkOrderState::DONE, WorkOrderState::CANCEL], true)),
                                    ]),
                                TextInput::make('expected_duration')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.expected-duration'))
                                    ->default('00:00')
                                    ->rule('regex:/^\d+:\d{2}$/')
                                    ->placeholder('00:00')
                                    ->afterStateHydrated(function (TextInput $component, mixed $state): void {
                                        $component->state(format_float_time((float) ($state ?: 0), 'minutes'));
                                    })
                                    ->dehydrateStateUsing(fn (?string $state): float => parse_float_time($state, 'minutes'))
                                    ->required()
                                    ->suffix(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.duration-suffix'))
                                    ->disabled(fn (?WorkOrder $record): bool => $record && in_array($record->state, [WorkOrderState::DONE, WorkOrderState::CANCEL], true)),
                            ]),
                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextInput::make('display_product')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.product'))
                                    ->afterStateHydrated(function (TextInput $component, mixed $state, ?WorkOrder $record): void {
                                        $component->state($record?->product?->name ?? $state ?? '—');
                                    })
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('display_quantity')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.quantity'))
                                    ->afterStateHydrated(function (TextInput $component, mixed $state, ?WorkOrder $record): void {
                                        $component->state(number_format((float) ($record?->quantity_remaining ?? $state ?? 0), 2));
                                    })
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('display_lot_serial')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.lot-serial'))
                                    ->afterStateHydrated(function (TextInput $component, mixed $state, ?WorkOrder $record): void {
                                        $component->state($record?->manufacturingOrder?->producingLot?->name ?? $state ?? '—');
                                    })
                                    ->disabled()
                                    ->dehydrated(false),
                                Select::make('manufacturing_order_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.manufacturing-order'))
                                    ->relationship('manufacturingOrder', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Order $record): string => static::getManufacturingOrderLabel($record))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->live()
                                    ->required()
                                    ->disabled(fn (?WorkOrder $record): bool => (bool) $record)
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                        $manufacturingOrder = Order::query()->with(['product', 'uom', 'producingLot'])->find($state);

                                        $set('product_id', $manufacturingOrder?->product_id);
                                        $set('uom_id', $manufacturingOrder?->uom_id);

                                        if ($manufacturingOrder?->product) {
                                            $set('display_product', $manufacturingOrder->product->name);
                                        }

                                        $set('display_quantity', (float) ($manufacturingOrder?->quantity_producing ?: $manufacturingOrder?->quantity ?: 0));

                                        static::syncExpectedDuration(
                                            $set,
                                            $get,
                                            Operation::query()->withTrashed()->find($get('operation_id'))
                                        );
                                    }),
                            ]),
                    ]),

                Tabs::make('work-order-tabs')
                    ->tabs([
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.title'))
                            ->schema([
                                static::getTimeTrackingRepeater(),
                                TextEntry::make('display_total_real_duration')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.footer.real-duration'))
                                    ->state(fn (?WorkOrder $record): string => format_float_time((float) ($record?->duration ?: 0), 'minutes').' (minutes)'),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.title'))
                            ->schema([
                                static::getComponentsRepeater(),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.work-instruction.title'))
                            ->schema([
                                View::make('manufacturing::filament.clusters.operations.resources.work-order.work-instruction-preview')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.blocked-by.title'))
                            ->schema([
                                Select::make('blockedByWorkOrders')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.blocked-by.fields.work-orders'))
                                    ->relationship(
                                        'blockedByWorkOrders',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, ?WorkOrder $record) => $query
                                            ->when($record, fn (Builder $relationQuery) => $relationQuery->whereKeyNot($record->getKey()))
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (WorkOrder $record): string => $record->display_name)
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->disabled(fn (?WorkOrder $record): bool => $record && ! in_array($record->state, [WorkOrderState::PENDING, WorkOrderState::WAITING], true)),
                            ]),
                    ]),

                Hidden::make('name'),
                Hidden::make('product_id'),
                Hidden::make('uom_id'),
                Hidden::make('display_quantity'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('manufacturingOrder')
                ->orderBy('sort')
                ->orderBy('calendar_leave_id')
                ->orderBy('started_at')
                ->orderBy('id'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.operation'))
                    ->searchable(),
                TextColumn::make('workCenter.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.work-center'))
                    ->searchable(),
                TextColumn::make('manufacturing_order_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.manufacturing-order'))
                    ->placeholder('—')
                    ->formatStateUsing(fn (mixed $state, WorkOrder $record): string => static::getManufacturingOrderLabel($record->manufacturingOrder))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('manufacturingOrder', function (Builder $relationQuery) use ($search): void {
                            $relationQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('reference', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('product.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.product'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('quantity_remaining')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.quantity-remaining'))
                    ->numeric(decimalPlaces: 2)
                    ->toggleable(),
                TextColumn::make('manufacturingOrder.producingLot.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.lot-serial'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('started_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.start'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('finished_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.end'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expected_duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.expected-duration'))
                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes'))
                    ->summarize([
                        Sum::make()
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.expected-duration'))
                            ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                    ]),
                TextColumn::make('duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.real-duration'))
                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes'))
                    ->summarize([
                        Sum::make()
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.real-duration'))
                            ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                    ]),
                TextColumn::make('state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.status'))
                    ->badge(),
            ])
            ->groups([
                TableGroup::make('state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.status')),
                TableGroup::make('workCenter.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.work-center')),
                TableGroup::make('manufacturingOrder.reference')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.manufacturing-order')),
                TableGroup::make('product.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.product')),
                TableGroup::make('started_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.start'))
                    ->date(),
                TableGroup::make('finished_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.end'))
                    ->date(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.work-order')),
                        SelectConstraint::make('state')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.status'))
                            ->multiple()
                            ->options(WorkOrderState::class)
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('operation')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.operation'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-cog-6-tooth'),
                        RelationshipConstraint::make('workCenter')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.work-center'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-cog-6-tooth'),
                        RelationshipConstraint::make('manufacturingOrder')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.manufacturing-order'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->getOptionLabelFromRecordUsing(fn (Order $record): string => static::getManufacturingOrderLabel($record))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-wrench-screwdriver'),
                        RelationshipConstraint::make('product')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.product'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-cube'),
                        DateConstraint::make('started_at')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.start')),
                        DateConstraint::make('finished_at')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.end')),
                        DateConstraint::make('created_at')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.updated-at')),
                    ]),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                Action::make('button_start')
                    ->hiddenLabel()
                    ->icon('heroicon-m-play-circle')
                    ->color('success')
                    ->size(Size::ExtraLarge)
                    ->visible(function (WorkOrder $record): bool {
                        $productionState = $record->manufacturingOrder?->state;

                        return ! in_array($productionState, [
                            ManufacturingOrderState::DRAFT,
                            ManufacturingOrderState::DONE,
                            ManufacturingOrderState::CANCEL,
                        ], true)
                            && $record->working_state !== WorkCenterWorkingState::BLOCKED
                            && ! in_array($record->state, [WorkOrderState::DONE, WorkOrderState::CANCEL], true)
                            && ! $record->is_user_working;
                    })
                    ->databaseTransaction()
                    ->action(function (WorkOrder $record): void {
                        $record->start();
                        $record->refresh();
                    }),
                Action::make('button_pending')
                    ->hiddenLabel()
                    ->icon('heroicon-m-pause-circle')
                    ->color('warning')
                    ->size(Size::ExtraLarge)
                    ->visible(function (WorkOrder $record): bool {
                        $productionState = $record->manufacturingOrder?->state;

                        return ! in_array($productionState, [
                            ManufacturingOrderState::DRAFT,
                            ManufacturingOrderState::DONE,
                            ManufacturingOrderState::CANCEL,
                        ], true)
                            && $record->working_state !== WorkCenterWorkingState::BLOCKED
                            && $record->is_user_working;
                    })
                    ->databaseTransaction()
                    ->action(function (WorkOrder $record): void {
                        $record->pending();
                        $record->refresh();
                    }),
                Action::make('button_done')
                    ->hiddenLabel()
                    ->icon('heroicon-m-check-circle')
                    ->color('primary')
                    ->size(Size::ExtraLarge)
                    ->visible(function (WorkOrder $record): bool {
                        $productionState = $record->manufacturingOrder?->state;

                        return ! in_array($productionState, [
                            ManufacturingOrderState::DRAFT,
                            ManufacturingOrderState::DONE,
                            ManufacturingOrderState::CANCEL,
                        ], true)
                            && $record->working_state !== WorkCenterWorkingState::BLOCKED
                            && $record->is_user_working;
                    })
                    ->databaseTransaction()
                    ->action(function (WorkOrder $record): void {
                        $record->finish();
                        $record->refresh();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(fn (?WorkOrder $record): array => static::getVisibleWorkOrderStateOptions($record?->state?->value ?? $record?->state))
                    ->disabled(),

                Section::make(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.title'))
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextEntry::make('operation.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.work-order'))
                                    ->placeholder('—'),
                                TextEntry::make('workCenter.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.work-center'))
                                    ->placeholder('—'),
                                TextEntry::make('product.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.product'))
                                    ->placeholder('—'),
                                TextEntry::make('quantity_remaining')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.quantity'))
                                    ->formatStateUsing(fn (mixed $state, WorkOrder $record): string => number_format((float) ($state ?: 0), 4).' '.($record->uom?->name ?? '—')),
                                TextEntry::make('manufacturingOrder.reference')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.manufacturing-order'))
                                    ->formatStateUsing(fn (mixed $state, WorkOrder $record): string => static::getManufacturingOrderLabel($record->manufacturingOrder)),
                                TextEntry::make('manufacturingOrder.producingLot.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.lot-serial'))
                                    ->placeholder('—'),
                            ]),
                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextEntry::make('started_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.start-date'))
                                    ->dateTime()
                                    ->placeholder('—'),
                                TextEntry::make('finished_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.end-date'))
                                    ->dateTime()
                                    ->placeholder('—'),
                                TextEntry::make('expected_duration')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.expected-duration'))
                                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                                TextEntry::make('duration')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.real-duration'))
                                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                            ]),
                    ]),

                Tabs::make('work-order-infolist-tabs')
                    ->tabs([
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.time-tracking.title'))
                            ->schema([
                                RepeatableEntry::make('productivityLogs')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('assignedUser.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.user')),
                                        InfolistTableColumn::make('duration')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.duration')),
                                        InfolistTableColumn::make('started_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.start-date')),
                                        InfolistTableColumn::make('finished_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.end-date')),
                                        InfolistTableColumn::make('loss.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.productivity')),
                                    ])
                                    ->schema([
                                        TextEntry::make('assignedUser.name')->hiddenLabel()->placeholder('—'),
                                        TextEntry::make('duration')->hiddenLabel()->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                                        TextEntry::make('started_at')->hiddenLabel()->dateTime()->placeholder('—'),
                                        TextEntry::make('finished_at')->hiddenLabel()->dateTime()->placeholder('—'),
                                        TextEntry::make('loss.name')->hiddenLabel()->placeholder('—'),
                                    ]),
                                TextEntry::make('duration')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.time-tracking.footer.real-duration'))
                                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes').' (minutes)'),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.components.title'))
                            ->schema([
                                RepeatableEntry::make('rawMaterialMoves')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('product.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.product')),
                                        InfolistTableColumn::make('sourceLocation.full_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.from')),
                                        InfolistTableColumn::make('product_uom_qty')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.to-consume')),
                                        InfolistTableColumn::make('uom.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.uom')),
                                    ])
                                    ->schema([
                                        TextEntry::make('product.name')->hiddenLabel()->placeholder('—'),
                                        TextEntry::make('sourceLocation.full_name')->hiddenLabel()->placeholder('—'),
                                        TextEntry::make('product_uom_qty')->hiddenLabel()->numeric(decimalPlaces: 4),
                                        TextEntry::make('uom.name')->hiddenLabel()->placeholder('—'),
                                    ]),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.work-instruction.title'))
                            ->schema([
                                ViewEntry::make('work_instruction_preview')
                                    ->hiddenLabel()
                                    ->view('manufacturing::filament.clusters.operations.resources.work-order.work-instruction-preview'),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.blocked-by.title'))
                            ->schema([
                                RepeatableEntry::make('blockedByWorkOrders')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('display_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.blocked-by.columns.work-order')),
                                        InfolistTableColumn::make('workCenter.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.blocked-by.columns.work-center')),
                                        InfolistTableColumn::make('state')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.blocked-by.columns.status')),
                                    ])
                                    ->schema([
                                        TextEntry::make('display_name')->hiddenLabel()->placeholder('—'),
                                        TextEntry::make('workCenter.name')->hiddenLabel()->placeholder('—'),
                                        TextEntry::make('state')->hiddenLabel()->badge(),
                                    ]),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkOrders::route('/'),
            'view'  => ViewWorkOrder::route('/{record}'),
            'edit'  => EditWorkOrder::route('/{record}/edit'),
        ];
    }

    protected static function getTimeTrackingRepeater(): Repeater
    {
        return Repeater::make('productivityLogs')
            ->relationship('productivityLogs')
            ->hiddenLabel()
            ->defaultItems(0)
            ->addActionLabel(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.add-action'))
            ->reorderable(false)
            ->compact()
            ->table([
                RepeaterTableColumn::make('assigned_user_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.user')),
                RepeaterTableColumn::make('duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.duration')),
                RepeaterTableColumn::make('started_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.start-date')),
                RepeaterTableColumn::make('finished_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.end-date')),
                RepeaterTableColumn::make('loss_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.productivity')),
            ])
            ->schema([
                Hidden::make('work_center_id')
                    ->default(fn (?WorkOrder $record): ?int => $record?->work_center_id),
                Hidden::make('company_id')
                    ->default(fn (?WorkOrder $record): ?int => $record?->manufacturingOrder?->company_id),
                Select::make('assigned_user_id')
                    ->hiddenLabel()
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->default(fn (): ?int => auth()->id())
                    ->required(),
                TextInput::make('duration')
                    ->hiddenLabel()
                    ->default('00:00')
                    ->live()
                    ->rule('regex:/^\d+:\d{2}$/')
                    ->placeholder('00:00')
                    ->afterStateHydrated(function (TextInput $component, mixed $state, ?WorkCenterProductivityLog $record): void {
                        if ($record) {
                            $component->state(format_float_time((float) ($state ?: 0), 'minutes'));
                        }
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, ?WorkCenterProductivityLog $record): void {
                        if ($record) {
                            return;
                        }

                        static::syncProductivityLogDatesFromDuration($set, $get, $state);
                    })
                    ->dehydrateStateUsing(fn (?string $state, ?WorkCenterProductivityLog $record): float => $record ? (float) ($record->duration ?: 0) : parse_float_time($state, 'minutes'))
                    ->disabled(fn (?WorkCenterProductivityLog $record): bool => (bool) $record)
                    ->required(),
                DateTimePicker::make('started_at')
                    ->hiddenLabel()
                    ->live()
                    ->native(false)
                    ->seconds(false)
                    ->default(fn (): Carbon => now())
                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state, ?WorkCenterProductivityLog $record): void {
                        if ($record) {
                            return;
                        }

                        static::syncProductivityLogDurationFromDates($set, $get, $state, $get('finished_at'));
                    })
                    ->required(),
                DateTimePicker::make('finished_at')
                    ->hiddenLabel()
                    ->live()
                    ->native(false)
                    ->seconds(false)
                    ->default(fn (): Carbon => now())
                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state, ?WorkCenterProductivityLog $record): void {
                        if ($record) {
                            return;
                        }

                        static::syncProductivityLogDurationFromDates($set, $get, $get('started_at'), $state);
                    }),
                Select::make('loss_id')
                    ->hiddenLabel()
                    ->relationship('loss', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->default(fn (): ?int => static::getDefaultProductivityLossId())
                    ->required(),
            ]);
    }

    protected static function getDefaultProductivityLossId(): ?int
    {
        return WorkCenterProductivityLoss::query()
            ->where('loss_type', 'productive')
            ->value('id');
    }

    protected static function getVisibleWorkOrderStateOptions(?string $currentState): array
    {
        $options = WorkOrderState::options();

        if ($currentState === WorkOrderState::CANCEL->value) {
            unset($options[WorkOrderState::DONE->value]);

            return $options;
        }

        unset($options[WorkOrderState::CANCEL->value]);

        return $options;
    }

    protected static function syncProductivityLogDatesFromDuration(Set $set, Get $get, ?string $durationState): void
    {
        $startedAt = static::parseProductivityLogDateState($get('started_at')) ?? now();
        $duration = (float) parse_float_time($durationState ?: '00:00', 'minutes');

        $set('started_at', $startedAt->format('Y-m-d H:i:s'));
        $set('finished_at', $startedAt->copy()->addSeconds((int) round($duration * 60))->format('Y-m-d H:i:s'));
    }

    protected static function syncProductivityLogDurationFromDates(Set $set, Get $get, mixed $startedAtState, mixed $finishedAtState): void
    {
        $startedAt = static::parseProductivityLogDateState($startedAtState);
        $finishedAt = static::parseProductivityLogDateState($finishedAtState);

        if (! $startedAt && ! $finishedAt) {
            return;
        }

        if (! $startedAt) {
            $startedAt = $finishedAt;
            $set('started_at', $startedAt?->format('Y-m-d H:i:s'));
        }

        if (! $finishedAt) {
            $finishedAt = $startedAt;
            $set('finished_at', $finishedAt?->format('Y-m-d H:i:s'));
        }

        $durationInSeconds = max(0, $startedAt->diffInSeconds($finishedAt, false));
        $set('duration', format_float_time($durationInSeconds / 60, 'minutes'));
    }

    protected static function parseProductivityLogDateState(mixed $state): ?Carbon
    {
        if ($state instanceof Carbon) {
            return $state;
        }

        if (blank($state)) {
            return null;
        }

        return Carbon::parse($state);
    }

    protected static function getComponentsRepeater(): Repeater
    {
        return Repeater::make('rawMaterialMoves')
            ->relationship('rawMaterialMoves')
            ->hiddenLabel()
            ->defaultItems(0)
            ->addable(false)
            ->deletable(false)
            ->reorderable(false)
            ->compact()
            ->table([
                RepeaterTableColumn::make('rendered_display_product')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.product')),
                RepeaterTableColumn::make('rendered_to_consume')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.to-consume')),
                RepeaterTableColumn::make('rendered_quantity')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.quantity')),
                RepeaterTableColumn::make('rendered_uom')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.uom')),
            ])
            ->schema([
                Hidden::make('work_order_id')
                    ->default(fn (?WorkOrder $record): ?int => $record?->id),
                Hidden::make('product_id'),
                Hidden::make('source_location_id'),
                Hidden::make('uom_id'),
                Hidden::make('product_uom_qty'),
                Hidden::make('quantity'),
                TextEntry::make('rendered_display_product')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => Product::query()->withTrashed()->find($get('product_id'))?->name ?? '—'),
                TextEntry::make('rendered_to_consume')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => number_format((float) ($get('product_uom_qty') ?: 0), 2)),
                TextEntry::make('rendered_quantity')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => number_format((float) ($get('quantity') ?: 0), 2)),
                TextEntry::make('rendered_uom')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => UOM::query()->find($get('uom_id'))?->name ?? '—'),
            ]);
    }

    protected static function syncExpectedDuration(Set $set, Get $get, ?Operation $operation): void
    {
        if (! $operation) {
            return;
        }

        $manufacturingOrder = Order::query()->with(['product', 'uom'])->find($get('manufacturing_order_id'));

        if (! $manufacturingOrder?->product) {
            return;
        }

        $quantity = (float) ($manufacturingOrder->quantity_producing ?: $manufacturingOrder->quantity ?: 0);

        $set('expected_duration', format_float_time($operation->getExpectedDuration($manufacturingOrder->product, $quantity), 'minutes'));
    }

    protected static function getManufacturingOrderLabel(?Order $order): string
    {
        if (! $order) {
            return '—';
        }

        return $order->reference ?: $order->name ?: 'MO/'.$order->getKey();
    }
}
