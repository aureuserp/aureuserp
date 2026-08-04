<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Schemas;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\Product;
use Webkul\Manufacturing\Models\WorkCenterProductivityLog;
use Webkul\Manufacturing\Models\WorkCenterProductivityLoss;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;
use Webkul\Support\Models\UOM;

class WorkOrderForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(fn (?WorkOrder $record): array => WorkOrderResource::getVisibleWorkOrderStateOptions($record?->state?->value ?? $record?->state))
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
                                    ->getOptionLabelFromRecordUsing(fn (Order $record): string => WorkOrderResource::getManufacturingOrderLabel($record))
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

                Section::make()
                    ->schema($customFormFields)
                    ->columns(2),
            ])
            ->columns(1);
    }

    private static function getTimeTrackingRepeater(): Repeater
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

    private static function getDefaultProductivityLossId(): ?int
    {
        return WorkCenterProductivityLoss::query()
            ->where('loss_type', 'productive')
            ->value('id');
    }

    private static function syncProductivityLogDatesFromDuration(Set $set, Get $get, ?string $durationState): void
    {
        $startedAt = static::parseProductivityLogDateState($get('started_at')) ?? now();
        $duration = (float) parse_float_time($durationState ?: '00:00', 'minutes');

        $set('started_at', $startedAt->format('Y-m-d H:i:s'));
        $set('finished_at', $startedAt->copy()->addSeconds((int) round($duration * 60))->format('Y-m-d H:i:s'));
    }

    private static function syncProductivityLogDurationFromDates(Set $set, Get $get, mixed $startedAtState, mixed $finishedAtState): void
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

    private static function parseProductivityLogDateState(mixed $state): ?Carbon
    {
        if ($state instanceof Carbon) {
            return $state;
        }

        if (blank($state)) {
            return null;
        }

        return Carbon::parse($state);
    }

    private static function getComponentsRepeater(): Repeater
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

    private static function syncExpectedDuration(Set $set, Get $get, ?Operation $operation): void
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
}
