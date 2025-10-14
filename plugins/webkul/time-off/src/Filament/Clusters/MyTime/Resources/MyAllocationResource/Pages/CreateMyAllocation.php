<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Models\Employee;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource;

class CreateMyAllocation extends CreateRecord
{
    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = MyAllocationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('time-off::filament/clusters/my-time/resources/my-allocation/pages/create-allocation.notification.success.title'))
            ->body(__('time-off::filament/clusters/my-time/resources/my-allocation/pages/create-allocation.notification.success.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $employee = Employee::where('user_id', Auth::id())->first();

        if (! $employee) {
            Notification::make()
                ->warning()
                ->title(__('time-off::filament/clusters/my-time/resources/my-allocation/pages/create-allocation.notification.warning.title'))
                ->body(__('time-off::filament/clusters/my-time/resources/my-allocation/pages/create-allocation.notification.warning.body'))
                ->send();

            $this->halt();

            return $data;
        }

        $data['employee_id'] = $employee->id;

        return $data;
    }
}
