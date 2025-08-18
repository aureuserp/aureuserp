<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListPurchaseAgreements extends ListRecords
{
    use HasTableViews;

    protected static string $resource = PurchaseAgreementResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/list-purchase-agreements.navigation.title');
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_agreements' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/list-purchase-agreements.tabs.my-agreements'))
                ->icon('heroicon-o-document-check')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),

            'blanket_orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/list-purchase-agreements.tabs.blanket-orders'))
                ->icon('heroicon-o-clipboard-document')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', RequisitionType::BLANKET_ORDER)),

            'purchase_templates' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/list-purchase-agreements.tabs.purchase-templates'))
                ->icon('heroicon-o-document-plus')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', RequisitionType::PURCHASE_TEMPLATE)),

            'draft' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/list-purchase-agreements.tabs.draft'))
                ->icon('heroicon-o-pencil-square')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', RequisitionState::DRAFT)),

            'done' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/list-purchase-agreements.tabs.done'))
                ->icon('heroicon-o-check-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', RequisitionState::CLOSED)),

            'archived' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/list-purchase-agreements.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/list-purchase-agreements.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
