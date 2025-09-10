<?php

namespace Webkul\Contact\Filament\Resources\Partners\Pages;

use Webkul\Contact\Filament\Resources\Partners\PartnerResource;
use Webkul\Partner\Filament\Resources\Partners\Pages\CreatePartner as BaseCreatePartner;

class CreatePartner extends BaseCreatePartner
{
    protected static string $resource = PartnerResource::class;
}
