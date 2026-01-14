<?php

namespace Webkul\Accounting\Models;

use Webkul\Account\Models\Move as BaseMove;

class Invoice extends BaseMove
{
    public function getResourceUrl(?string $page = 'edit'): ?string
    {
        if (! $this->id || ! $this->move_type) {
            return null;
        }

        $resourceClass = match ($this->move_type) {
            \Webkul\Account\Enums\MoveType::OUT_INVOICE => \Webkul\Accounting\Filament\Clusters\Customer\Resources\InvoiceResource::class,
            \Webkul\Account\Enums\MoveType::OUT_REFUND  => \Webkul\Accounting\Filament\Clusters\Customer\Resources\CreditNoteResource::class,
            \Webkul\Account\Enums\MoveType::IN_INVOICE  => \Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource::class,
            \Webkul\Account\Enums\MoveType::IN_REFUND   => \Webkul\Accounting\Filament\Clusters\Vendors\Resources\RefundResource::class,
            default                                     => null,
        };

        return $resourceClass
            ? $resourceClass::getUrl($page, ['record' => $this->id])
            : null;
    }
}
