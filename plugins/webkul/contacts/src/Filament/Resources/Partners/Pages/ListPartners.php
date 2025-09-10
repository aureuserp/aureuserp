<?php

namespace Webkul\Contact\Filament\Resources\Partners\Pages;

use Webkul\Contact\Filament\Resources\Partners\PartnerResource;
use Webkul\Partner\Filament\Resources\Partners\Pages\ListPartners as BaseListPartners;

class ListPartners extends BaseListPartners
{
    protected static string $resource = PartnerResource::class;
}
