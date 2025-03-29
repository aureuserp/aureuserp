<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentsResource\Pages;

use Webkul\Account\Filament\Resources\PaymentsResource\Pages\CreatePayments as BaseCreatePayments;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentsResource;

class CreatePayments extends BaseCreatePayments
{
    protected static string $resource = PaymentsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);

        $data['partner_type'] = 'customer';

        return $data;
    }
}
