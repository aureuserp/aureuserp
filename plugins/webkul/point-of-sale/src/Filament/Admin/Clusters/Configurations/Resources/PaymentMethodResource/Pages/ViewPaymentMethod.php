<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewPaymentMethod extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(fn (DeleteAction $action) => PaymentMethodResource::haltWhenInOpenSession($action, [$this->getRecord()])),
        ];
    }
}
