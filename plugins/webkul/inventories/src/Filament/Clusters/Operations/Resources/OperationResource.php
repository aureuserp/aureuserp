<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource\Schemas\OperationForm;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource\Schemas\OperationInfolist;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource\Tables\OperationsTable;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Settings\ProductSettings;
use Webkul\TableViews\Filament\Components\PresetView;

class OperationResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Operation::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('inventories::filament/clusters/operations/resources/operation.global-search.partner') => $record->partner?->name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return OperationForm::configure($schema);
    }

    public static function mergeTableConstraints(array $baseConstraints, array $include = [], array $exclude = []): array
    {
        return static::mergeCustomTableQueryBuilderConstraints($baseConstraints, $include, $exclude);
    }

    public static function table(Table $table): Table
    {
        return OperationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationInfolist::configure($schema);
    }

    public static function getUrl(?string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        return match ($parameters['record']?->operationType->type) {
            Enums\OperationType::INCOMING => ReceiptResource::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
            Enums\OperationType::INTERNAL => InternalResource::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
            Enums\OperationType::OUTGOING => DeliveryResource::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
            Enums\OperationType::DROPSHIP => DropshipResource::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
            default                       => parent::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
        };
    }

    public static function getPresetTableViews(): array
    {
        return [
            'todo' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.todo'))
                ->favorite()
                ->icon('heroicon-s-clipboard-document-list')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('state', [OperationState::DONE, OperationState::CANCELED])),
            'my' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.my'))
                ->favorite()
                ->icon('heroicon-s-user')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),
            'favorite' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.starred'))
                ->favorite()
                ->icon('heroicon-s-star')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_favorite', true)),
            'draft' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.draft'))
                ->favorite()
                ->icon('heroicon-s-pencil-square')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OperationState::DRAFT)),
            'waiting' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.waiting'))
                ->favorite()
                ->icon('heroicon-s-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OperationState::CONFIRMED)),
            'ready' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.ready'))
                ->favorite()
                ->icon('heroicon-s-play-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OperationState::ASSIGNED)),
            'done' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.done'))
                ->favorite()
                ->icon('heroicon-s-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OperationState::DONE)),
            'canceled' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.canceled'))
                ->icon('heroicon-s-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OperationState::CANCELED)),
            'backorders' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.back-orders'))
                ->icon('heroicon-s-arrow-uturn-left')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('back_order_id')),
        ];
    }

    public static function getOperationSettings(): OperationSettings
    {
        return once(fn () => app(OperationSettings::class));
    }

    public static function getProductSettings(): ProductSettings
    {
        return once(fn () => app(ProductSettings::class));
    }

    public static function getTraceabilitySettings(): TraceabilitySettings
    {
        return once(fn () => app(TraceabilitySettings::class));
    }

    public static function getWarehouseSettings(): WarehouseSettings
    {
        return once(fn () => app(WarehouseSettings::class));
    }
}
