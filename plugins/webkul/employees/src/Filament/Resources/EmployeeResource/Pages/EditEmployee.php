<?php

namespace Webkul\Employee\Filament\Resources\EmployeeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Support\Models\ActivityPlan;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('employees::filament/resources/employee/pages/edit-employee.notification.title'))
            ->body(__('employees::filament/resources/employee/pages/edit-employee.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getActivityPlans()),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('employees::filament/resources/employee/pages/edit-employee.header-actions.delete.notification.title'))
                        ->body(__('employees::filament/resources/employee/pages/edit-employee.header-actions.delete.notification.body')),
                ),
        ];
    }

    private function getActivityPlans(): mixed
    {
        return ActivityPlan::where('plugin', 'employees')->pluck('name', 'id');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $partner = $this->record->partner;

        return [
            ...$data,
            ...$partner ? $partner->toArray() : [],
        ];
    }
}
