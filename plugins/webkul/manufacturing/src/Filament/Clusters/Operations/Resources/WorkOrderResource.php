<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Filament\Clusters\Operations;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Pages\EditWorkOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Pages\ListWorkOrders;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Pages\ViewWorkOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Schemas\WorkOrderForm;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Schemas\WorkOrderInfolist;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Tables\WorkOrdersTable;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Manufacturing\Settings\OperationSettings;

class WorkOrderResource extends Resource
{
    use HasCustomFields;

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('manufacturingOrder');
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
        return WorkOrderForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return WorkOrdersTable::configure(
            $table,
            static::getCustomTableColumns(),
            static::getCustomTableFilters(),
        );
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkOrderInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkOrders::route('/'),
            'view'  => ViewWorkOrder::route('/{record}'),
            'edit'  => EditWorkOrder::route('/{record}/edit'),
        ];
    }

    public static function getVisibleWorkOrderStateOptions(?string $currentState): array
    {
        $options = WorkOrderState::options();

        if ($currentState === WorkOrderState::CANCEL->value) {
            unset($options[WorkOrderState::DONE->value]);

            return $options;
        }

        unset($options[WorkOrderState::CANCEL->value]);

        return $options;
    }

    public static function getManufacturingOrderLabel(?Order $order): string
    {
        if (! $order) {
            return '—';
        }

        return $order->reference ?: $order->name ?: 'MO/'.$order->getKey();
    }
}
