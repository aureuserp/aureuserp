<?php

namespace Webkul\Account\Filament\Resources\RefundResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Account\Filament\Resources\RefundResource;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewRefund extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = RefundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->setResource($this->getResource()),
            BaseActions\PayAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\ResetToDraftAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            DeleteAction::make(),
        ];
    }
}
