<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\IncotermResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\IncotermResource;
use Webkul\Account\Filament\Resources\IncotermResource\Pages\ManageIncoterms as BaseManageIncoterms;

class ManageIncoterms extends BaseManageIncoterms
{
    protected static string $resource = IncotermResource::class;
}
