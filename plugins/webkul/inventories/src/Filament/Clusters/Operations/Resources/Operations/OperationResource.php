<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Operations;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Deliveries\DeliveryResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Internals\InternalResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Operations\Schemas\OperationForm;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Operations\Schemas\OperationInfolist;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Operations\Tables\OperationsTable;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Receipts\ReceiptResource;
use Webkul\Inventory\Models\Operation;
use Webkul\TableViews\Filament\Components\PresetView;

class OperationResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Operation::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return OperationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OperationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationInfolist::configure($schema);
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return match ($parameters['record']?->operationType->type) {
            Enums\OperationType::INCOMING => ReceiptResource::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
            Enums\OperationType::INTERNAL => InternalResource::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
            Enums\OperationType::OUTGOING => DeliveryResource::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
            default                       => parent::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
        };
    }

    public static function getPresetTableViews(): array
    {
        return [
            'todo_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.todo'))
                ->favorite()
                ->icon('heroicon-s-clipboard-document-list')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('state', [OperationState::DONE, OperationState::CANCELED])),
            'my_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.my'))
                ->favorite()
                ->icon('heroicon-s-user')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),
            'favorite_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.starred'))
                ->favorite()
                ->icon('heroicon-s-star')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_favorite', true)),
            'draft_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.draft'))
                ->favorite()
                ->icon('heroicon-s-pencil-square')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OperationState::DRAFT)),
            'waiting_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.waiting'))
                ->favorite()
                ->icon('heroicon-s-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OperationState::CONFIRMED)),
            'ready_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.ready'))
                ->favorite()
                ->icon('heroicon-s-play-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OperationState::ASSIGNED)),
            'done_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.done'))
                ->favorite()
                ->icon('heroicon-s-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OperationState::DONE)),
            'canceled_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.canceled'))
                ->icon('heroicon-s-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OperationState::CANCELED)),
        ];
    }
}
