<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;

class CreateOpportunity extends CreateRecord
{
    protected static string $resource = OpportunityResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('crm::filament/clusters/sales/resources/opportunity/pages/create-opportunity.notification.title'))
            ->body(__('crm::filament/clusters/sales/resources/opportunity/pages/create-opportunity.notification.body'));
    }
}
