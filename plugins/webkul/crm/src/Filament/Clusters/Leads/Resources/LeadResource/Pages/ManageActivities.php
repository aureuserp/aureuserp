<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ManageActivities as BaseManageActivities;

class ManageActivities extends BaseManageActivities
{
    protected static string $resource = LeadResource::class;
}
