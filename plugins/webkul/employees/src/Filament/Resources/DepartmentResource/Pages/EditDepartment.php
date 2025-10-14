<?php

namespace Webkul\Employee\Filament\Resources\DepartmentResource\Pages;

use Exception;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Employee\Filament\Resources\DepartmentResource;

class EditDepartment extends EditRecord
{
    protected static string $resource = DepartmentResource::class;

    private bool $updateFailed = false;

    protected function getRedirectUrl(): ?string
    {
        if ($this->updateFailed) {
            return null;
        }

        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        if ($this->updateFailed) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title(__('employees::filament/resources/department/pages/edit-department.update.success.notification.title'))
            ->body(__('employees::filament/resources/department/pages/edit-department.update.success.notification.body'));
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
                        ->title(__('employees::filament/resources/department/pages/edit-department.header-actions.delete.notification.title'))
                        ->body(__('employees::filament/resources/department/pages/edit-department.header-actions.delete.notification.body')),
                ),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            $record->update($data);

            return $record;
        } catch (Exception $e) {
            $this->updateFailed = true;

            Notification::make()
                ->danger()
                ->title(__('employees::filament/resources/department/pages/edit-department.update.error.notification.title'))
                ->body($e->getMessage())
                ->send();

            return $record;
        }
    }
}
