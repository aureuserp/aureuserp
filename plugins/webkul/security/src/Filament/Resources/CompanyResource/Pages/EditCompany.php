<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Pages;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Security\Filament\Resources\CompanyResource;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('security::filament/resources/company/pages/edit-company.notification.title'))
            ->body(__('security::filament/resources/company/pages/edit-company.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->hidden(fn () => User::where('default_company_id', $this->record->id)->exists())
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('security::filament/resources/company/pages/edit-company.header-actions.delete.notification.title'))
                        ->body(__('security::filament/resources/company/pages/edit-company.header-actions.delete.notification.body'))
                ),
        ];
    }
}
