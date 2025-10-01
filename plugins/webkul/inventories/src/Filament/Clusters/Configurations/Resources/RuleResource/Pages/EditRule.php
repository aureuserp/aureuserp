<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource;

class EditRule extends EditRecord
{
    protected static string $resource = RuleResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/rule/pages/edit-rule.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/rule/pages/edit-rule.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/rule/pages/edit-rule.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/rule/pages/edit-rule.header-actions.delete.notification.body')),
                ),
        ];
    }
}
