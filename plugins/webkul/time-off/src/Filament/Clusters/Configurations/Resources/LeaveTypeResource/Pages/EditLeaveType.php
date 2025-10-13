<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource;

class EditLeaveType extends EditRecord
{
    protected static string $resource = LeaveTypeResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('time-off::filament/clusters/configurations/resources/leave-type/pages/edit-leave-type.notification.title'))
            ->body(__('time-off::filament/clusters/configurations/resources/leave-type/pages/edit-leave-type.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/clusters/configurations/resources/leave-type/pages/edit-leave-type.header-actions.delete.notification.title'))
                        ->body(__('time-off::filament/clusters/configurations/resources/leave-type/pages/edit-leave-type.header-actions.delete.notification.body'))
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();

        $data['company_id'] = $user->default_company_id;

        $data['creator_id'] = $user?->id;

        return $data;
    }
}
