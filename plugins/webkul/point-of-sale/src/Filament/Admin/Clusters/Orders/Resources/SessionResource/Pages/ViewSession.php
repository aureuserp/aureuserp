<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\CashMovementAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\CloseSessionAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\ConfirmOpeningControlAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\OpenRescueSessionAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewSession extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = SessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->activityPlans($this->getRecord()->activityPlans())
                ->resource($this->getResource()),
            ConfirmOpeningControlAction::make(),
            CashMovementAction::make(),
            CloseSessionAction::make(),
            OpenRescueSessionAction::make(),
        ];
    }
}
