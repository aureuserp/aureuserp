<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('crm::filament/clusters/configurations/resources/tag/pages/manage-tags.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('crm::filament/clusters/configurations/resources/tag/pages/manage-tags.header-actions.create.notification.title'))
                        ->body(__('crm::filament/clusters/configurations/resources/tag/pages/manage-tags.header-actions.create.notification.body')),
                ),
        ];
    }
}
