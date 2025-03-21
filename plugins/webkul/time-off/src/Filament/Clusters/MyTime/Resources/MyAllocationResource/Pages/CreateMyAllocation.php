<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource;

class CreateMyAllocation extends CreateRecord
{
    protected static string $resource = MyAllocationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('time-off::filament/clusters/my-time/resources/my-allocation/pages/create-allocation.notification.title'))
            ->body(__('time-off::filament/clusters/my-time/resources/my-allocation/pages/create-allocation.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if ($user?->employee) {
            $data['employee_id'] = $user->employee->id;
        }

        return $data;
    }
}
