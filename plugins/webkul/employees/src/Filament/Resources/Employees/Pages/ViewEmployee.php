<?php

namespace Webkul\Employee\Filament\Resources\Employees\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Employee\Filament\Resources\Employees\EmployeeResource;
use Webkul\Support\Models\ActivityPlan;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/employee/pages/view-employee.sub-navigation.view-employee');
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->setResource(static::$resource)
                ->setActivityPlans($this->getActivityPlans()),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('employees::filament/resources/employee/pages/view-employee.header-actions.delete.notification.title'))
                        ->body(__('employees::filament/resources/employee/pages/view-employee.header-actions.delete.notification.body')),
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
