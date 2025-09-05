<?php

namespace Webkul\Website\Filament\Admin\Resources\Partner\Pages;

use Webkul\Partner\Filament\Resources\Partners\Pages\ManageContacts as BaseManageContacts;
use Webkul\Website\Filament\Admin\Resources\Partner\PartnerResource;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = PartnerResource::class;
}
