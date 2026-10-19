<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\EditTeam as BaseEditTeam;

class EditTeam extends BaseEditTeam
{
    use HasCrmClusterNavigation;

    protected static string $resource = TeamResource::class;
}
