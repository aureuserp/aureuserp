<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource;

class EditMyAllocation extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = MyAllocationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('time-off::filament/clusters/my-time/resources/my-allocation/pages/edit-allocation.notification.title'))
            ->body(__('time-off::filament/clusters/my-time/resources/my-allocation/pages/edit-allocation.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->setResource(static::$resource),
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/clusters/my-time/resources/my-allocation/pages/edit-allocation.header-actions.delete.notification.title'))
                        ->body(__('time-off::filament/clusters/my-time/resources/my-allocation/pages/edit-allocation.header-actions.delete.notification.body'))
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();

        if ($user?->employee) {
            $data['employee_id'] = $user->employee->id;
        }

        return $data;
    }
}
