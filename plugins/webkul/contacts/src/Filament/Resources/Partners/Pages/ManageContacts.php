<?php

namespace Webkul\Contact\Filament\Resources\Partners\Pages;

use Webkul\Contact\Filament\Resources\Partners\PartnerResource;
use Webkul\Partner\Filament\Resources\Partners\Pages\ManageContacts as BaseManageContacts;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = PartnerResource::class;
}
