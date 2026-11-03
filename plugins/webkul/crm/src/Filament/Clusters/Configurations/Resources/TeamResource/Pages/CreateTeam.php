<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\ManagesCRMTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\CreateTeam as BaseCreateTeam;

class CreateTeam extends BaseCreateTeam
{
    use HasCRMClusterNavigation, ManagesCRMTeam;

    protected static string $resource = TeamResource::class;
}
