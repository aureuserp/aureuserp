<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class CreateRule extends CreateRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = RuleResource::class;

    protected ?bool $hasDatabaseTransactions = true;

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
            ->title(__('inventories::filament/clusters/configurations/resources/rule/pages/create-rule.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/rule/pages/create-rule.notification.body'));
    }
}
