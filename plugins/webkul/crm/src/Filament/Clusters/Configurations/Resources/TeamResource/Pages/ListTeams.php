<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\ListTeams as BaseListTeams;

class ListTeams extends BaseListTeams
{
    protected static string $resource = TeamResource::class;
}
