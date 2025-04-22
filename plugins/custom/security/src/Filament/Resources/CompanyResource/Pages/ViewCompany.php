<?php

namespace Custom\Security\Filament\Resources\CompanyResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Custom\Security\Filament\Resources\CompanyResource;

class ViewCompany extends ViewRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('security::filament/resources/company/pages/view-company.header-actions.delete.notification.title'))
                        ->body(__('security::filament/resources/company/pages/view-company.header-actions.delete.notification.body'))
                ),
        ];
    }
}
