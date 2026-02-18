<?php

namespace Webkul\Support\Filament\Resources\CompanyResource\Pages;

use Webkul\Security\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Support\Filament\Resources\CompanyResource;

class ViewCompany extends ViewRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->hidden(fn () => User::where('default_company_id', $this->record->id)->exists())
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/company/pages/view-company.header-actions.delete.notification.title'))
                        ->body(__('support::filament/resources/company/pages/view-company.header-actions.delete.notification.body'))
                ),
        ];
    }
}
