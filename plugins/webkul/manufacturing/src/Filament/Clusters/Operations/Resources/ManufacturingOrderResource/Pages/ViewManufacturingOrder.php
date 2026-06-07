<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages;

use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\CancelAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\ConfirmAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\PlanAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\Print\PrintLabelsAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\Print\PrintMOAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\StartAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\UnplanAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewManufacturingOrder extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ManufacturingOrderResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->reference ?: __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/view-manufacturing-order.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            ConfirmAction::make('confirm'),
            PlanAction::make('plan'),
            UnplanAction::make('unplan'),
            StartAction::make('start'),
            CancelAction::make('cancel'),
            ActionGroup::make([
                PrintMOAction::make(),
                PrintLabelsAction::make(),
            ])
                ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/view-manufacturing-order.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->button(),
        ];
    }

    public function updateForm(): void
    {
    }
}
