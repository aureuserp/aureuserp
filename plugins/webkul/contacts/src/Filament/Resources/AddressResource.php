<?php

namespace Webkul\Contact\Filament\Resources;

use Webkul\Partner\Filament\Resources\AddressResource as BaseAddressResource;
use Webkul\Contact\Models\Address;

class AddressResource extends BaseAddressResource
{
    protected static ?string $model = Address::class;

    protected static bool $shouldRegisterNavigation = false;
}
