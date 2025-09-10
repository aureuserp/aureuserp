<?php

namespace Webkul\Contact\Filament\Resources\Partners\Pages;

use Webkul\Contact\Filament\Resources\Partners\PartnerResource;
use Webkul\Partner\Filament\Resources\Partners\Pages\EditPartner as BaseEditPartner;

class EditPartner extends BaseEditPartner
{
    protected static string $resource = PartnerResource::class;
}
