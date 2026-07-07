<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources;

use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\TextSize;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\ComponentAttributeBag;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Enums\WorkCenterWorkingState;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Filament\Clusters\Operations;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\CreateManufacturingOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\EditManufacturingOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\ListManufacturingOrders;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\ManageTransfers;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\OverviewManufacturingOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\ViewManufacturingOrder;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\BillOfMaterialLine;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\Product;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Manufacturing\Settings\OperationSettings;
use Webkul\Product\Enums\ProductType;
use Webkul\Security\Traits\HasResourcePermissionQuery;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;
use Webkul\Support\Models\UOM;

class ManufacturingOrderResource extends Resource
{
    use HasResourcePermissionQuery;
    
    protected static ?string $model = Order::class;

    protected static ?string $cluster = Operations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getModelLabel(): string
    {
        return __('manufacturing::models/order.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/manufacturing-order.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/manufacturing-order.navigation.group');
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
                    ->options(ManufacturingOrderState::options())
                    ->options(function (?Order $record, Get $get): array {
                        $options = ManufacturingOrderState::options();
                        $currentState = $get('state');

                        if ($currentState && ! $currentState instanceof ManufacturingOrderState) {
                            $currentState = ManufacturingOrderState::from($currentState);
                        }

                        $currentState ??= $record?->state;

                        if (! $record) {
                            unset(
                                $options[ManufacturingOrderState::PROGRESS->value],
                                $options[ManufacturingOrderState::TO_CLOSE->value],
                                $options[ManufacturingOrderState::CANCEL->value],
                            );
                        } else {
                            foreach ([ManufacturingOrderState::PROGRESS, ManufacturingOrderState::TO_CLOSE, ManufacturingOrderState::CANCEL] as $state) {
                                if ($currentState !== $state) {
                                    unset($options[$state->value]);
                                }
                            }

                            if ($currentState) {
                                $options[$currentState->value] = $currentState->getLabel();
                            }
                        }

                        return $options;
                    })
                    ->default(ManufacturingOrderState::DRAFT)
                    ->disabled()
                    ->dehydrated(),

                Section::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.title'))
                    ->columns(2)
                    ->schema(fn ($record) => [
                        Group::make()
                            ->columns(1)
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        fn (Builder $query) => $query
                                            ->withTrashed()
                                            ->where('type', ProductType::GOODS)
                                            ->whereNull('is_configurable')
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Product $record): string => $record->name)
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->live()
                                    ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->wrapOptionLabels(false)
                                    ->disableOptionWhen(function ($label, $value, $state, $component) {
                                        $isDeleted = str_contains($label, ' (Deleted)');

                                        $isDuplicate = false;

                                        if ($component?->getParentRepeater()) {
                                            $repeater = $component->getParentRepeater();

                                            $isDuplicate = collect($repeater->getState())
                                                ->pluck(
                                                    (string) str($component->getStatePath())
                                                        ->after("{$repeater->getStatePath()}.")
                                                        ->after('.'),
                                                )
                                                ->flatten()
                                                ->diff(Arr::wrap($state))
                                                ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                                                ->contains($value);
                                        }

                                        return $isDeleted || $isDuplicate;
                                    })
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                        $product = Product::query()->withTrashed()->find($state);

                                        if (! $product) {
                                            $set('uom_id', null);
                                            $set('bill_of_material_id', null);
                                            $set('rawMaterialMoves', []);
                                            $set('workOrders', []);

                                            return;
                                        }

                                        $set('uom_id', $product->uom_id ?: static::getDefaultUomId());

                                        $set('company_id', $product->company_id ?? Auth::user()?->default_company_id);

                                        $billOfMaterialId = static::getDefaultBillOfMaterialId($product);

                                        if (! $billOfMaterialId) {
                                            $set('bill_of_material_id', null);
                                            $set('rawMaterialMoves', []);
                                            $set('workOrders', []);

                                            return;
                                        }

                                        if ($get('bill_of_material_id') !== $billOfMaterialId) {
                                            $set('bill_of_material_id', $billOfMaterialId);
                                        }

                                        static::applyBillOfMaterialDefaults(
                                            $set,
                                            BillOfMaterial::query()->withTrashed()->find($billOfMaterialId),
                                            $product,
                                            (float) ($get('quantity') ?: 1),
                                            $get('uom_id'),
                                        );
                                    })
                                    ->required(),
                                static::getQuantityUomField(),
                                Select::make('bill_of_material_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.bill-of-material'))
                                    ->relationship(
                                        'billOfMaterial',
                                        'code',
                                        modifyQueryUsing: function (Get $get, Builder $query): void {
                                            $product = Product::query()->withTrashed()->find($get('product_id'));

                                            if (! $product) {
                                                $query->whereRaw('1 = 0');

                                                return;
                                            }

                                            $productIds = array_filter([$product->id, $product->parent_id]);

                                            $query->withTrashed()->whereIn('product_id', $productIds);
                                        }
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (BillOfMaterial $record): string => static::getBillOfMaterialLabel($record))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->live()
                                    ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                        $product = Product::query()->withTrashed()->find($get('product_id'));

                                        static::applyBillOfMaterialDefaults(
                                            $set,
                                            BillOfMaterial::query()->withTrashed()->find($state),
                                            $product,
                                            (float) ($get('quantity') ?: 1),
                                            $get('uom_id'),
                                        );
                                    }),
                            ]),
                        Group::make()
                            ->columns(1)
                            ->schema([
                                DateTimePicker::make('started_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.scheduled-date'))
                                    ->native(false)
                                    ->default(now())
                                    ->seconds(false)
                                    ->required()
                                    ->disabled(fn (?Order $record) => $record && in_array($record->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])),
                                DateTimePicker::make('finished_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.scheduled-end'))
                                    ->default(now())
                                    ->seconds(false)
                                    ->disabled()
                                    ->visible(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT),
                                Select::make('assigned_user_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.responsible'))
                                    ->relationship('assignedUser', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->default(Auth::id())
                                    ->disabled(fn (?Order $record) => $record && in_array($record->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])),
                            ]),
                    ]),

                Tabs::make('manufacturing-order-tabs')
                    ->tabs([
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.title'))
                            ->schema([
                                static::getComponentsRepeater(),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.title'))
                            ->visible(static::getOperationSettings()->enable_work_orders)
                            ->schema([
                                static::getWorkOrdersRepeater(),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.title'))
                            ->hidden(! static::getOperationSettings()->enable_byproducts)
                            ->schema([
                                TextEntry::make('by_products_process_note')
                                    ->hiddenLabel()
                                    ->state(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.process-note')),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.miscellaneous.title'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('operation_type_id')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.miscellaneous.fields.operation-type'))
                                            ->relationship(
                                                'operationType',
                                                'name',
                                                fn (Builder $query) => $query
                                                    ->withTrashed()
                                                    ->where('type', 'manufacture')
                                            )
                                            ->getOptionLabelFromRecordUsing(fn (OperationType $record): string => static::getOperationTypeLabel($record))
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->wrapOptionLabels(false)
                                            ->live()
                                            ->required()
                                            ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                                $operationType = OperationType::query()->withTrashed()->find($state);

                                                $sourceLocationId = $operationType?->source_location_id;
                                                $set('source_location_id', $sourceLocationId);
                                                $set('destination_location_id', $operationType?->destination_location_id);

                                                $sourceLocation = $sourceLocationId ? Location::query()->withTrashed()->find($sourceLocationId) : null;
                                                $displayFrom = $sourceLocation?->full_name ?? '—';

                                                foreach (array_keys($get('rawMaterialMoves') ?? []) as $key) {
                                                    $set("rawMaterialMoves.{$key}.operation_type_id", $operationType?->id);
                                                    $set("rawMaterialMoves.{$key}.source_location_id", $sourceLocationId);
                                                    $set("rawMaterialMoves.{$key}.display_from", $displayFrom);
                                                }
                                            }),
                                        Select::make('source_location_id')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.miscellaneous.fields.source'))
                                            ->relationship('sourceLocation', 'full_name', fn (Builder $query) => $query->withTrashed())
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->native(false)
                                            ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                            ->visible(static::getWarehouseSettings()->enable_locations)
                                            ->wrapOptionLabels(false)
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                                $sourceLocation = $state ? Location::query()->withTrashed()->find($state) : null;
                                                $displayFrom = $sourceLocation?->full_name ?? '—';

                                                foreach (array_keys($get('rawMaterialMoves') ?? []) as $key) {
                                                    $set("rawMaterialMoves.{$key}.source_location_id", $state);
                                                    $set("rawMaterialMoves.{$key}.display_from", $displayFrom);
                                                }
                                            }),
                                        Select::make('destination_location_id')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.miscellaneous.fields.finished-products-location'))
                                            ->relationship('destinationLocation', 'full_name', fn (Builder $query) => $query->withTrashed())
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->native(false)
                                            ->wrapOptionLabels(false)
                                            ->live()
                                            ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                            ->visible(static::getWarehouseSettings()->enable_locations)
                                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                                $set('destination_location_id', $state);
                                            }),
                                        Select::make('company_id')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.miscellaneous.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                            ->default(Auth::user()?->default_company_id),
                                    ]),
                            ]),
                    ]),

                Hidden::make('destination_location_id'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.reference'))
                    ->searchable(),
                TextColumn::make('started_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.start'))
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('finished_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.end'))
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deadline_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.deadline'))
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('product.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.product'))
                    ->searchable(),
                TextColumn::make('producingLot.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.lot-serial-number'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bill_of_material_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.bill-of-material'))
                    ->formatStateUsing(fn (mixed $state, Order $record): string => static::getBillOfMaterialLabel($record->billOfMaterial))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('origin')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.source'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.responsible'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reservation_state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.mo-readiness'))
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('components_availability')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.component-status'))
                    ->badge()
                    ->state(fn (Order $record): ?string => $record->components_availability)
                    ->color(fn (Order $record): string => match ($record->components_availability_state) {
                        'available'   => 'success',
                        'unavailable' => 'danger',
                        'late'        => 'warning',
                        'expected'    => 'info',
                        default       => 'gray',
                    })
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quantity')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.quantity'))
                    ->numeric(decimalPlaces: 4),
                TextColumn::make('uom.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.uom'))
                    ->placeholder('—'),
                TextColumn::make('consumption_efficiency')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.consumption-efficiency'))
                    ->state(fn (Order $record): string => $record->consumption_efficiency !== null ? $record->consumption_efficiency.'%' : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('work_orders_expected_duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.expected-duration'))
                    ->state(fn (Order $record): string => format_float_time((float) ($record->workOrders->sum('expected_duration') ?: 0), 'minutes'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('work_orders_real_duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.real-duration'))
                    ->state(fn (Order $record): string => format_float_time((float) ($record->workOrders->sum('duration') ?: 0), 'minutes'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.company'))
                    ->placeholder('—'),
                TextColumn::make('state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.state'))
                    ->badge(),
            ])
            ->groups([
                TableGroup::make('state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.groups.state')),
                TableGroup::make('product.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.groups.product')),
                TableGroup::make('billOfMaterial.reference')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.groups.bill-of-material')),
                TableGroup::make('assignedUser.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.groups.responsible')),
                TableGroup::make('deadline_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.groups.deadline'))
                    ->date(),
            ])
            ->recordTitleAttribute('name')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function (Order $record): array {
                        $options = ManufacturingOrderState::options();

                        unset(
                            $options[ManufacturingOrderState::PROGRESS->value],
                            $options[ManufacturingOrderState::TO_CLOSE->value],
                            $options[ManufacturingOrderState::CANCEL->value],
                        );

                        if ($record->state === ManufacturingOrderState::CANCEL) {
                            $options[ManufacturingOrderState::CANCEL->value] = ManufacturingOrderState::CANCEL->getLabel();
                        }

                        return $options;
                    }),

                Section::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.title'))
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.product'))
                                    ->size(TextSize::Large)
                                    ->placeholder('—'),
                                TextEntry::make('quantity')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.quantity'))
                                    ->state(function (Order $record): string {
                                        $expectedQuantity = number_format((float) $record->quantity, 4, '.', '');

                                        if ($record->state === ManufacturingOrderState::DRAFT) {
                                            return $expectedQuantity.' '.($record->uom?->name ?? '—');
                                        }

                                        $producingQuantity = number_format((float) ($record->quantity_producing ?: 0), 4, '.', '');

                                        return $producingQuantity.' / '.$expectedQuantity.' '.($record->uom?->name ?? '—');
                                    }),
                                TextEntry::make('bill_of_material_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.bill-of-material'))
                                    ->state(fn (Order $record): string => static::getBillOfMaterialLabel($record->billOfMaterial)),
                                TextEntry::make('consumption_efficiency')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.consumption-efficiency'))
                                    ->state(fn (Order $record): string => $record->consumption_efficiency !== null ? $record->consumption_efficiency.'%' : '—'),
                            ]),
                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextEntry::make('started_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.scheduled-date'))
                                    ->dateTime(),
                                TextEntry::make('finished_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.scheduled-end'))
                                    ->dateTime()
                                    ->placeholder('—')
                                    ->visible(fn (Order $record): bool => $record->state !== ManufacturingOrderState::DRAFT),
                                TextEntry::make('assignedUser.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.responsible'))
                                    ->placeholder('—'),
                            ]),
                    ]),
                Tabs::make('manufacturing-order-details')
                    ->tabs([
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.components.title'))
                            ->schema([
                                RepeatableEntry::make('rawMaterialMoves')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('product.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.component')),
                                        InfolistTableColumn::make('sourceLocation.full_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.from'))
                                            ->visible(static::getWarehouseSettings()->enable_locations),
                                        InfolistTableColumn::make('product_uom_qty')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.to-consume')),
                                        InfolistTableColumn::make('quantity')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.quantity'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        InfolistTableColumn::make('uom.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.uom')),
                                    ])
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('sourceLocation.full_name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('product_uom_qty')
                                            ->hiddenLabel()
                                            ->numeric(decimalPlaces: 4),
                                        TextEntry::make('quantity')
                                            ->hiddenLabel()
                                            ->numeric(decimalPlaces: 4)
                                            ->placeholder('—'),
                                        TextEntry::make('uom.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                    ]),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.work-orders.title'))
                            ->schema([
                                RepeatableEntry::make('workOrders')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('operation.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.operation')),
                                        InfolistTableColumn::make('workCenter.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.work-center')),
                                        InfolistTableColumn::make('product.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.product')),
                                        InfolistTableColumn::make('quantity_remaining')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.quantity-remaining')),
                                        InfolistTableColumn::make('started_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.start'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        InfolistTableColumn::make('finished_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.end'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        InfolistTableColumn::make('expected_duration')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.expected-duration')),
                                        InfolistTableColumn::make('duration')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.real-duration')),
                                        InfolistTableColumn::make('state')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.status')),
                                    ])
                                    ->schema([
                                        TextEntry::make('operation.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('workCenter.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('product.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('quantity_remaining')
                                            ->hiddenLabel()
                                            ->numeric(decimalPlaces: 4)
                                            ->placeholder('—'),
                                        TextEntry::make('started_at')
                                            ->hiddenLabel()
                                            ->dateTime()
                                            ->placeholder('—'),
                                        TextEntry::make('finished_at')
                                            ->hiddenLabel()
                                            ->dateTime()
                                            ->placeholder('—'),
                                        TextEntry::make('expected_duration')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                                        TextEntry::make('duration')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                                        TextEntry::make('state')
                                            ->hiddenLabel()
                                            ->badge(),
                                    ]),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.by-products.title'))
                            ->hidden(! static::getOperationSettings()->enable_byproducts)
                            ->schema([
                                RepeatableEntry::make('moveByproducts')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('product.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.columns.product')),
                                        InfolistTableColumn::make('destinationLocation.full_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.columns.to')),
                                        InfolistTableColumn::make('product_uom_qty')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.columns.to-produce')),
                                        InfolistTableColumn::make('uom.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.columns.uom')),
                                    ])
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('destinationLocation.full_name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('product_uom_qty')
                                            ->hiddenLabel()
                                            ->numeric(decimalPlaces: 4),
                                        TextEntry::make('uom.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                    ]),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.miscellaneous.title'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('operationType.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.miscellaneous.entries.operation-type'))
                                            ->formatStateUsing(fn (mixed $state, Order $record): string => static::getOperationTypeLabel($record->operationType)),
                                        TextEntry::make('sourceLocation.full_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.miscellaneous.entries.source'))
                                            ->placeholder('—')
                                            ->visible(static::getWarehouseSettings()->enable_locations),
                                        TextEntry::make('finalLocation.full_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.miscellaneous.entries.finished-products-location'))
                                            ->placeholder('—')
                                            ->visible(static::getWarehouseSettings()->enable_locations),
                                        TextEntry::make('company.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.miscellaneous.entries.company'))
                                            ->placeholder('—'),
                                    ]),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewManufacturingOrder::class,
            EditManufacturingOrder::class,
            OverviewManufacturingOrder::class,
            ManageTransfers::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'     => ListManufacturingOrders::route('/'),
            'create'    => CreateManufacturingOrder::route('/create'),
            'view'      => ViewManufacturingOrder::route('/{record}'),
            'edit'      => EditManufacturingOrder::route('/{record}/edit'),
            'overview'  => OverviewManufacturingOrder::route('/{record}/overview'),
            'transfers' => ManageTransfers::route('/{record}/transfers'),
        ];
    }

    protected static function getDefaultBillOfMaterialId(Product $product): ?int
    {
        $productIds = array_filter([$product->id, $product->parent_id]);

        return BillOfMaterial::query()
            ->withTrashed()
            ->whereIn('product_id', $productIds)
            ->orderByDesc('product_id')
            ->value('id');
    }

    protected static function applyBillOfMaterialDefaults(
        Set $set,
        ?BillOfMaterial $billOfMaterial,
        ?Product $product = null,
        float $quantity = 1,
        ?int $uomId = null
    ): void {
        if (! $billOfMaterial) {
            $set('rawMaterialMoves', []);

            $set('workOrders', []);

            return;
        }

        if ($billOfMaterial->operation_type_id) {
            $operationType = OperationType::query()->withTrashed()->find($billOfMaterial->operation_type_id);
        } else {
            $operationType = OperationType::query()->withTrashed()->where('type', 'manufacture')->first();
        }

        $set('operation_type_id', $operationType->id);

        $set('source_location_id', $operationType?->source_location_id);

        $set('destination_location_id', $operationType?->destination_location_id);

        if (! $uomId) {
            $set('uom_id', $billOfMaterial->uom_id ?: static::getDefaultUomId());
        }

        $set('company_id', $billOfMaterial->company_id);

        $effectiveQuantity = static::convertOrderQuantityToBillOfMaterialUom($billOfMaterial, $quantity, $uomId);

        $set('rawMaterialMoves', static::getComponentRepeaterState($billOfMaterial, $effectiveQuantity));

        $set('workOrders', static::getWorkOrderRepeaterState(
            $billOfMaterial,
            $product ?? $billOfMaterial->product,
            $effectiveQuantity,
        ));
    }

    protected static function getBillOfMaterialLabel(?BillOfMaterial $billOfMaterial): string
    {
        if (! $billOfMaterial) {
            return '—';
        }

        $reference = $billOfMaterial->code ?: (string) $billOfMaterial->id;
        $productName = $billOfMaterial->product?->name;

        if (! $productName) {
            return $reference;
        }

        return $reference.': '.$productName;
    }

    protected static function getOperationTypeLabel(?OperationType $operationType): string
    {
        if (! $operationType) {
            return '—';
        }

        if (! $operationType->warehouse) {
            return $operationType->name;
        }

        return $operationType->warehouse->name.': '.$operationType->name;
    }

    protected static function getQuantityUomField(): FusedGroup
    {
        return FusedGroup::make([
            TextInput::make('quantity_producing')
                ->numeric()
                ->minValue(0)
                ->step('0.0001')
                ->default(0)
                ->live(debounce: 300)
                ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                    $billOfMaterial = BillOfMaterial::query()->withTrashed()->find($get('bill_of_material_id'));
                    $product = Product::query()->withTrashed()->find($get('product_id'));

                    static::applyQuantityProducingDefaults(
                        $set,
                        $billOfMaterial,
                        $product,
                        (float) ($state ?: 0),
                        $get('uom_id'),
                        $get('rawMaterialMoves') ?? [],
                        $get('workOrders') ?? [],
                    );
                })
                ->required(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                ->hidden(fn (?Order $record) => ! $record || $record->state === ManufacturingOrderState::DRAFT)
                ->dehydrated(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                ->disabled(fn (?Order $record) => $record && in_array($record->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL]))
                ->columnSpan(1),
            TextInput::make('quantity')
                ->numeric()
                ->minValue(0.0001)
                ->step('0.0001')
                ->default(1)
                ->live(debounce: 300)
                ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                    $billOfMaterial = BillOfMaterial::query()->withTrashed()->find($get('bill_of_material_id'));
                    $product = Product::query()->withTrashed()->find($get('product_id'));

                    static::applyBillOfMaterialDefaults(
                        $set,
                        $billOfMaterial,
                        $product,
                        (float) ($state ?: 1),
                        $get('uom_id'),
                    );
                })
                ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                ->prefix(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT ? '/' : null)
                ->required()
                ->columnSpan(fn (?Order $record): int => $record && $record->state !== ManufacturingOrderState::DRAFT ? 2 : 3),
            Select::make('uom_id')
                ->hiddenLabel()
                ->native(false)
                ->required()
                ->searchable()
                ->preload()
                ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                ->live()
                ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                    $billOfMaterial = BillOfMaterial::query()->withTrashed()->find($get('bill_of_material_id'));

                    if (! $billOfMaterial) {
                        return;
                    }

                    $uomId = $state ? (int) $state : null;
                    $product = Product::query()->withTrashed()->find($get('product_id'));
                    $stateValue = $get('state');
                    $isDraft = $stateValue === null || $stateValue === ManufacturingOrderState::DRAFT->value;

                    if ($isDraft) {
                        $effectiveQuantity = static::convertOrderQuantityToBillOfMaterialUom(
                            $billOfMaterial,
                            (float) ($get('quantity') ?: 1),
                            $uomId,
                        );

                        $quantityMultiplier = $billOfMaterial->getQuantityMultiplier($effectiveQuantity);

                        $bomLinesById = $billOfMaterial->lines()->get()->keyBy('id');

                        $updatedMoves = [];

                        foreach (($get('rawMaterialMoves') ?? []) as $key => $move) {
                            $bomLineId = $move['bom_line_id'] ?? null;

                            if ($bomLineId && $bomLinesById->has($bomLineId)) {
                                $move['product_uom_qty'] = round((float) $bomLinesById->get($bomLineId)->quantity * $quantityMultiplier, 4);
                            }

                            $updatedMoves[$key] = $move;
                        }

                        $set('rawMaterialMoves', $updatedMoves);

                        $updatedWorkOrders = [];

                        foreach (($get('workOrders') ?? []) as $key => $workOrder) {
                            $workOrder['quantity_remaining'] = round($effectiveQuantity, 4);

                            $updatedWorkOrders[$key] = $workOrder;
                        }

                        $set('workOrders', $updatedWorkOrders);

                        return;
                    }

                    static::applyQuantityProducingDefaults(
                        $set,
                        $billOfMaterial,
                        $product,
                        (float) ($get('quantity_producing') ?: 0),
                        $uomId,
                        $get('rawMaterialMoves') ?? [],
                        $get('workOrders') ?? [],
                    );
                })
                ->relationship(
                    'uom',
                    'name',
                    function (Builder $query, Get $get): Builder {
                        $product = Product::query()->withTrashed()->find($get('product_id'));
                        $categoryId = $product?->uom?->category_id;

                        return $query
                            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                            ->orderBy('name');
                    },
                )
                ->default(fn (Get $get): ?int => Product::query()->withTrashed()->find($get('product_id'))?->uom_id)
                ->placeholder(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.uom-placeholder'))
                ->columnSpan(1),
        ])
            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.quantity'))
            ->columns(4);
    }

    protected static function getComponentsRepeater(): Repeater
    {
        return Repeater::make('rawMaterialMoves')
            ->relationship('rawMaterialMoves')
            ->hiddenLabel()
            ->defaultItems(0)
            ->addActionLabel(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.add-action'))
            ->addable(fn (?Order $record): bool => ! in_array($record?->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL]))
            ->deletable(true)
            ->reorderable(false)
            ->compact()
            ->table(fn ($record) => [
                RepeaterTableColumn::make('product_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.component')),
                RepeaterTableColumn::make('rendered_display_from')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.from'))
                    ->visible(static::getWarehouseSettings()->enable_locations),
                RepeaterTableColumn::make('product_uom_qty')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.to-consume')),
                RepeaterTableColumn::make('quantity')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.quantity'))
                    ->visible(fn () => $record?->rawMaterialMoves->contains(fn ($move) => $move->id && $move->state !== MoveState::DRAFT)),
                RepeaterTableColumn::make('uom_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.uom')),
                RepeaterTableColumn::make('rendered_display_forecast')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.forecast'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->schema([
                Hidden::make('name'),
                Hidden::make('operation_type_id')
                    ->default(fn (Get $get): mixed => $get('../../operation_type_id')),
                Hidden::make('bom_line_id'),
                Hidden::make('source_location_id')
                    ->default(fn (Get $get): mixed => $get('../../source_location_id')),
                Hidden::make('display_from')
                    ->default(function (Get $get): string {
                        $sourceLocation = Location::query()->withTrashed()->find($get('../../source_location_id'));

                        return $sourceLocation?->full_name ?? '—';
                    }),
                Hidden::make('display_forecast'),
                Select::make('product_id')
                    ->hiddenLabel()
                    ->relationship(
                        'product',
                        'name',
                        fn (Builder $query) => $query
                            ->withTrashed()
                            ->where('type', ProductType::GOODS)
                            ->whereNull('is_configurable'),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->live()
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->wrapOptionLabels(false)
                    ->disableOptionWhen(function ($label, $value, $state, $component) {
                        $isDeleted = str_contains($label, ' (Deleted)');

                        $isDuplicate = false;

                        if ($component?->getParentRepeater()) {
                            $repeater = $component->getParentRepeater();

                            $isDuplicate = collect($repeater->getState())
                                ->pluck(
                                    (string) str($component->getStatePath())
                                        ->after("{$repeater->getStatePath()}.")
                                        ->after('.'),
                                )
                                ->flatten()
                                ->diff(Arr::wrap($state))
                                ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                                ->contains($value);
                        }

                        return $isDeleted || $isDuplicate;
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        $product = Product::query()->withTrashed()->find($state);

                        $uomId = $product?->uom_id ?: static::getDefaultUomId();

                        $set('uom_id', $uomId);
                    })
                    ->required()
                    ->disabled(fn ($record): bool => $record instanceof Move && $record->id && $record->state !== MoveState::DRAFT),
                TextEntry::make('rendered_display_from')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        $sourceLocation = Location::query()->withTrashed()->find($get('source_location_id'));

                        if ($sourceLocation) {
                            return $sourceLocation->full_name;
                        }

                        return (string) ($get('display_from') ?: '—');
                    }),
                TextInput::make('product_uom_qty')
                    ->hiddenLabel()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->live(onBlur: true)
                    ->required()
                    ->suffix(function (Get $get, $record): mixed {
                        if (
                            $get('../../state') !== ManufacturingOrderState::DRAFT->value
                            || ! $get('product_id')
                            || (float) ($get('product_uom_qty') ?? 0) <= 0
                            || ($record?->forecast_availability ?? 1) > 0
                        ) {
                            return null;
                        }

                        return \Filament\Support\generate_icon_html(
                            'heroicon-o-exclamation-triangle',
                            null,
                            (new ComponentAttributeBag)
                                ->color(IconComponent::class, 'danger')
                                ->class(['fi-text-color-600'])
                                ->merge([
                                    'style'         => 'color: var(--text)',
                                    'x-tooltip.raw' => __('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.to-consume-tooltip'),
                                ], escape: false),
                        );
                    })
                    ->disabled(fn ($record): bool => $record instanceof Move && $record->id && $record->state !== MoveState::DRAFT),
                TextInput::make('quantity')
                    ->hiddenLabel()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->visible(fn ($record): bool => $record instanceof Move && $record->id && $record->state !== MoveState::DRAFT)
                    ->disabled(fn ($record): bool => $record instanceof Move && in_array($record->state, [MoveState::DONE, MoveState::CANCELED])),
                // ->suffixAction(fn (Move $record) => static::getMoveLinesAction($record)),
                Select::make('uom_id')
                    ->hiddenLabel()
                    ->default(fn (Get $get): ?int => Product::query()->withTrashed()->find($get('product_id'))?->uom_id)
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get): Builder {
                            $product = Product::query()->withTrashed()->find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query
                                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                                ->orderBy('name');
                        },
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->required()
                    ->disabled(fn ($record): bool => $record instanceof Move && $record->id && $record->state !== MoveState::DRAFT),
                TextEntry::make('rendered_display_forecast')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => (string) ($get('display_forecast') ?: '—')),
            ]);
    }

    protected static function getWorkOrdersRepeater(): Repeater
    {
        return Repeater::make('workOrders')
            ->relationship('workOrders')
            ->hiddenLabel()
            ->defaultItems(0)
            ->addActionLabel(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.add-action'))
            ->addable(fn (?Order $record): bool => ! in_array($record?->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL]))
            ->deletable(true)
            ->reorderable(false)
            ->compact()
            ->extraItemActions([
                Action::make('openWorkOrder')
                    ->tooltip(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.actions.open-work-order.tooltip'))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (array $arguments, Get $get): ?string => filled($get("workOrders.{$arguments['item']}.id"))
                        ? WorkOrderResource::getUrl('view', [
                            'record' => $get("workOrders.{$arguments['item']}.id"),
                        ])
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn (array $arguments, Get $get): bool => filled($get("workOrders.{$arguments['item']}.id"))),
            ])
            ->table(fn ($record) => [
                RepeaterTableColumn::make('name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.operation')),
                RepeaterTableColumn::make('work_center_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.work-center')),
                RepeaterTableColumn::make('rendered_display_product')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.product')),
                RepeaterTableColumn::make('rendered_display_quantity_remaining')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.quantity-remaining'))
                    ->visible(fn () => $record && ! in_array($record->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])),
                RepeaterTableColumn::make('rendered_display_quantity_produced')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.quantity-produced'))
                    ->visible(fn () => $record && in_array($record->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])),
                RepeaterTableColumn::make('started_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.start'))
                    ->toggleable(isToggledHiddenByDefault: true),
                RepeaterTableColumn::make('finished_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.end'))
                    ->toggleable(isToggledHiddenByDefault: true),
                RepeaterTableColumn::make('expected_duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.expected-duration')),
                RepeaterTableColumn::make('rendered_display_real_duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.real-duration')),
                RepeaterTableColumn::make('state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.status'))
                    ->visible(fn () => $record && $record?->state !== ManufacturingOrderState::DRAFT)
                    ->resizable(),
            ])
            ->schema([
                Hidden::make('operation_id'),
                TextInput::make('name')
                    ->hiddenLabel()
                    ->required()
                    ->disabled(fn ($record): bool => $record && ! in_array($record->state, [WorkOrderState::PENDING, WorkOrderState::WAITING])),
                Hidden::make('product_id')
                    ->default(fn (Get $get): mixed => $get('../../product_id')),
                Hidden::make('duration')
                    ->default(0),
                Hidden::make('quantity_remaining')
                    ->default(fn (Get $get): float => (float) ($get('../../quantity') ?: 0)),
                Hidden::make('display_product')
                    ->default(function (Get $get): string {
                        $product = Product::query()->withTrashed()->find($get('../../product_id'));

                        return $product?->name ?? '—';
                    }),

                Select::make('work_center_id')
                    ->hiddenLabel()
                    ->relationship(
                        'workCenter',
                        'name',
                        fn (Builder $query) => $query->withTrashed(),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->required()
                    ->disabled(fn ($record): bool => $record && ! in_array($record->state, [WorkOrderState::PENDING, WorkOrderState::WAITING])),
                TextEntry::make('rendered_display_product')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        $workOrderId = $get('id');

                        if ($workOrderId) {
                            $workOrder = WorkOrder::query()->with(['product'])->find($workOrderId);

                            if ($workOrder?->product) {
                                return $workOrder->product->name;
                            }
                        }

                        $product = Product::query()->withTrashed()->find($get('product_id'));

                        if ($product) {
                            return $product->name;
                        }

                        return (string) ($get('display_product') ?: '—');
                    }),
                TextEntry::make('rendered_display_quantity_remaining')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        $workOrderId = $get('id');

                        if ($workOrderId) {
                            $workOrder = WorkOrder::query()->find($workOrderId);

                            if ($workOrder) {
                                return number_format((float) $workOrder->quantity_remaining, 4);
                            }
                        }

                        return number_format((float) ($get('quantity_remaining') ?: 0), 4);
                    }),
                TextEntry::make('rendered_display_quantity_produced')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => number_format((float) ($get('quantity_produced') ?: 0), 4)),
                DateTimePicker::make('started_at')
                    ->hiddenLabel()
                    ->live()
                    ->native(false)
                    ->seconds(true)
                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                        if (blank($state)) {
                            return;
                        }

                        $expectedDuration = (float) parse_float_time((string) ($get('expected_duration') ?: '00:00'), 'minutes');
                        $finishedAt = Carbon::parse($state)
                            ->addSeconds((int) round($expectedDuration * 60));

                        $set(
                            'finished_at',
                            $finishedAt->format('Y-m-d H:i:s')
                        );
                    })
                    ->disabled(fn ($record): bool => $record && ! in_array($record->state, [WorkOrderState::PENDING, WorkOrderState::WAITING])),
                DateTimePicker::make('finished_at')
                    ->hiddenLabel()
                    ->native(false)
                    ->seconds(true)
                    ->disabled(fn ($record): bool => $record && ! in_array($record->state, [WorkOrderState::PENDING, WorkOrderState::WAITING])),
                TextInput::make('expected_duration')
                    ->hiddenLabel()
                    ->default('00:00')
                    ->afterStateHydrated(function (TextInput $component, mixed $state): void {
                        $component->state(format_float_time((float) ($state ?: 0), 'minutes'));
                    })
                    ->dehydrateStateUsing(fn (?string $state): float => parse_float_time($state, 'minutes'))
                    ->required()
                    ->disabled(fn ($record): bool => in_array($record?->state, [WorkOrderState::DONE, WorkOrderState::CANCEL])),
                TextEntry::make('rendered_display_real_duration')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => format_float_time((float) ($get('duration') ?: 0), 'minutes')),
                TextEntry::make('state')
                    ->hiddenLabel()
                    ->badge()
                    ->suffixActions([
                        Action::make('button_start')
                            ->icon('heroicon-m-play-circle')
                            ->color('success')
                            ->size(Size::ExtraLarge)
                            ->databaseTransaction()
                            ->visible(function (?WorkOrder $record): bool {
                                if (! $record) {
                                    return false;
                                }

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
                            ->action(function (WorkOrder $record, Set $set): void {
                                $record->start();

                                $record->refresh();
                                $record->manufacturingOrder?->refresh();

                                static::syncWorkOrderDisplayState($set, $record);
                                static::syncManufacturingOrderDisplayState($set, $record->manufacturingOrder);
                            }),
                        Action::make('button_pending')
                            ->icon('heroicon-m-pause-circle')
                            ->color('warning')
                            ->size(Size::ExtraLarge)
                            ->databaseTransaction()
                            ->visible(function (?WorkOrder $record): bool {
                                if (! $record) {
                                    return false;
                                }

                                $productionState = $record->manufacturingOrder?->state;

                                return ! in_array($productionState, [
                                    ManufacturingOrderState::DRAFT,
                                    ManufacturingOrderState::DONE,
                                    ManufacturingOrderState::CANCEL,
                                ], true)
                                    && $record->working_state !== WorkCenterWorkingState::BLOCKED
                                    && $record->is_user_working;
                            })
                            ->action(function (WorkOrder $record, Set $set): void {
                                $record->pending();

                                $record->refresh();
                                $record->manufacturingOrder?->refresh();

                                static::syncWorkOrderDisplayState($set, $record);
                                static::syncManufacturingOrderDisplayState($set, $record->manufacturingOrder);
                            }),
                        Action::make('button_done')
                            ->icon('heroicon-m-check-circle')
                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.actions.done.label'))
                            ->color('primary')
                            ->size(Size::ExtraLarge)
                            ->databaseTransaction()
                            ->visible(function (?WorkOrder $record): bool {
                                if (! $record) {
                                    return false;
                                }

                                $productionState = $record->manufacturingOrder?->state;

                                return ! in_array($productionState, [
                                    ManufacturingOrderState::DRAFT,
                                    ManufacturingOrderState::DONE,
                                    ManufacturingOrderState::CANCEL,
                                ], true)
                                    && $record->working_state !== WorkCenterWorkingState::BLOCKED
                                    && $record->is_user_working;
                            })
                            ->action(function (WorkOrder $record, Set $set): void {
                                $record->finish();

                                $record->refresh();
                                $record->manufacturingOrder?->refresh();

                                static::syncWorkOrderDisplayState($set, $record);
                                static::syncManufacturingOrderDisplayState($set, $record->manufacturingOrder);
                            }),
                    ]),
            ]);
    }

    protected static function syncWorkOrderDisplayState(Set $set, WorkOrder $workOrder): void
    {
        $set('state', $workOrder->state?->value);
        $set('started_at', $workOrder->started_at);
        $set('finished_at', $workOrder->finished_at);
        $set('duration', (float) ($workOrder->duration ?: 0));
        $set('quantity_remaining', (float) $workOrder->quantity_remaining);
        $set('display_product', $workOrder->product?->name ?? '—');
    }

    protected static function syncManufacturingOrderDisplayState(Set $set, ?Order $order): void
    {
        if (! $order) {
            return;
        }

        $set('../../state', $order->state?->value);
        $set('../../started_at', $order->started_at);
        $set('../../finished_at', $order->finished_at);
        $set('../../deadline_at', $order->deadline_at);
        $set('../../quantity_producing', (float) ($order->quantity_producing ?: 0));
        $set('../../reservation_state', $order->reservation_state?->value);
    }

    protected static function getComponentRepeaterState(BillOfMaterial $billOfMaterial, float $quantity): array
    {
        $quantityMultiplier = $billOfMaterial->getQuantityMultiplier($quantity);

        if ($billOfMaterial->operation_type_id) {
            $operationType = $billOfMaterial->operationType;
        } else {
            $operationType = OperationType::query()->withTrashed()->where('type', 'manufacture')->first();
        }

        return $billOfMaterial->lines()
            ->with(['product', 'uom'])
            ->orderBy('sort')
            ->get()
            ->map(fn (BillOfMaterialLine $line): array => [
                'bom_line_id'        => $line->id,
                'product_id'         => $line->product_id,
                'uom_id'             => $line->uom_id,
                'product_uom_qty'    => round((float) $line->quantity * $quantityMultiplier, 4),
                'operation_type_id'  => $operationType->id,
                'source_location_id' => $operationType->source_location_id,
                'display_from'       => $operationType?->sourceLocation?->full_name ?? '—',
                'display_forecast'   => '—',
            ])
            ->values()
            ->all();
    }

    protected static function getWorkOrderRepeaterState(BillOfMaterial $billOfMaterial, ?Product $product, float $quantity): array
    {
        $product ??= $billOfMaterial->product;

        return $billOfMaterial->operations()
            ->with(['workCenter'])
            ->orderBy('sort')
            ->get()
            ->map(fn (Operation $operation): array => [
                'operation_id'              => $operation->id,
                'name'                      => $operation->name,
                'work_center_id'            => $operation->work_center_id,
                'product_id'                => $product?->id,
                'expected_duration'         => format_float_time($operation->getExpectedDuration($product, $quantity), 'minutes'),
                'duration'                  => 0,
                'quantity_remaining'        => round($quantity, 4),
                'display_product'           => $product?->name ?? '—',
            ])
            ->values()
            ->all();
    }

    protected static function getDefaultUomId(): ?int
    {
        return UOM::query()
            ->where('name', 'Units')
            ->value('id')
            ?? UOM::query()->value('id');
    }

    protected static function applyQuantityProducingDefaults(
        Set $set,
        ?BillOfMaterial $billOfMaterial,
        ?Product $product,
        float $quantityProducing,
        ?int $uomId,
        array $rawMaterialMoves,
        array $workOrders,
    ): void {
        if (! $billOfMaterial) {
            return;
        }

        $effectiveQuantity = static::convertOrderQuantityToBillOfMaterialUom(
            $billOfMaterial,
            $quantityProducing,
            $uomId,
        );

        $quantityMultiplier = $billOfMaterial->getQuantityMultiplier($effectiveQuantity);
        $bomLinesById = $billOfMaterial->lines()->get()->keyBy('id');
        $operationsById = $billOfMaterial->operations()->with(['workCenter'])->get()->keyBy('id');

        $updatedMoves = [];

        foreach ($rawMaterialMoves as $key => $move) {
            $bomLineId = $move['bom_line_id'] ?? null;

            if ($bomLineId && $bomLinesById->has($bomLineId)) {
                $move['quantity'] = round((float) $bomLinesById->get($bomLineId)->quantity * $quantityMultiplier, 4);
            }

            $updatedMoves[$key] = $move;
        }

        $set('rawMaterialMoves', $updatedMoves);

        $updatedWorkOrders = [];

        foreach ($workOrders as $key => $workOrder) {
            $operationId = $workOrder['operation_id'] ?? null;

            if ($operationId && $operationsById->has($operationId)) {
                $operation = $operationsById->get($operationId);
                $workOrder['expected_duration'] = format_float_time($operation->getExpectedDuration($product, $effectiveQuantity), 'minutes');
            }

            $workOrder['quantity_remaining'] = round($effectiveQuantity, 4);
            $updatedWorkOrders[$key] = $workOrder;
        }

        $set('workOrders', $updatedWorkOrders);
    }

    protected static function convertOrderQuantityToBillOfMaterialUom(BillOfMaterial $billOfMaterial, float $quantity, ?int $uomId): float
    {
        if (! $uomId || ! $billOfMaterial->uom_id || $uomId === $billOfMaterial->uom_id) {
            return $quantity;
        }

        $selectedUom = UOM::query()->withTrashed()->find($uomId);

        if (! $selectedUom || ! $billOfMaterial->uom) {
            return $quantity;
        }

        return (float) $selectedUom->computeQuantity($quantity, $billOfMaterial->uom, roundingMethod: 'HALF-UP');
    }

    public static function getWarehouseSettings(): WarehouseSettings
    {
        return settings(WarehouseSettings::class);
    }

    public static function getOperationSettings(): OperationSettings
    {
        return settings(OperationSettings::class);
    }
}
