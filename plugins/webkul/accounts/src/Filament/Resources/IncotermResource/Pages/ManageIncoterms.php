<?php

namespace Webkul\Account\Filament\Resources\IncotermResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Account\Filament\Resources\IncotermResource;

class ManageIncoterms extends ListRecords
{
    protected static string $resource = IncotermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/incoterm/pages/manage-incoterms.header-actions.notification.title'))
                        ->body(__('accounts::filament/resources/incoterm/pages/manage-incoterms.header-actions.notification.body'))
                ),
        ];
    }
}
