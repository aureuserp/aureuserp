<?php

namespace Webkul\Contact\Filament\Resources;

use Webkul\Contact\Models\Address;
use Webkul\Partner\Filament\Resources\AddressResource as BaseAddressResource;

class AddressResource extends BaseAddressResource
{
    protected static ?string $model = Address::class;

    protected static bool $shouldRegisterNavigation = false;
}
