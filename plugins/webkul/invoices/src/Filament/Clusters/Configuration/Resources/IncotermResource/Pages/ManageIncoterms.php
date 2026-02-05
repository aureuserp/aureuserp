<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncotermResource\Pages;

use Webkul\Account\Filament\Resources\IncotermResource\Pages\ManageIncoterms as BaseManageIncoterms;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncotermResource;

class ManageIncoterms extends BaseManageIncoterms
{
    protected static string $resource = IncotermResource::class;
}
