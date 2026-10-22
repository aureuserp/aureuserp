<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Filament\Concerns\ManagesCrmTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\ViewTeam as BaseViewTeam;

class ViewTeam extends BaseViewTeam
{
    use HasCrmClusterNavigation, ManagesCrmTeam;

    protected static string $resource = TeamResource::class;
}
