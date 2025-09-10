<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments\Pages;

use Webkul\Account\Filament\Resources\Payments\Pages\CreatePayments as BaseCreatePayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments\PaymentsResource;

class CreatePayments extends BaseCreatePayments
{
    protected static string $resource = PaymentsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);

        $data['partner_type'] = 'supplier';

        return $data;
    }
}
