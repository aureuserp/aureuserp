<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource;

class ManageMailServers extends ManageRecords
{
    protected static string $resource = MailServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('crm::filament/clusters/configurations/resources/mail-server.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('crm::filament/clusters/configurations/resources/mail-server.header-actions.create.notification')),
                ),
        ];
    }
}
