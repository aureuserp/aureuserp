<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncoTerms\Pages;

use Webkul\Account\Filament\Resources\IncoTerms\Pages\ListIncoTerms as BaseListIncoTerms;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncoTerms\IncoTermResource;

class ListIncoTerms extends BaseListIncoTerms
{
    protected static string $resource = IncoTermResource::class;
}
