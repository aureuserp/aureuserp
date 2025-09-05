<?php

namespace Webkul\Website\Filament\Admin\Resources\Partner\Pages;

use Webkul\Partner\Filament\Resources\Partners\Pages\ManageAddresses as BaseManageAddresses;
use Webkul\Website\Filament\Admin\Resources\Partner\PartnerResource;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = PartnerResource::class;
}
