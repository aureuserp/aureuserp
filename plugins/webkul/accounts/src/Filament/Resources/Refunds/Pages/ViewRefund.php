<?php

namespace Webkul\Account\Filament\Resources\Refunds\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\Invoices\Actions as BaseActions;
use Webkul\Account\Filament\Resources\Refunds\RefundResource;
use Webkul\Chatter\Filament\Actions as ChatterActions;

class ViewRefund extends ViewRecord
{
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
