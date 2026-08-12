<?php

namespace Webkul\Security\Filament\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Webkul\Security\Filament\Resources\RoleResource;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected ?bool $hasUnsavedDataChangesAlert = false;

    protected Collection $permissions;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action, Model $record): void {
                    if (RoleResource::isProtectedRoleRecord($record)) {
                        Notification::make()
                            ->danger()
                            ->title(__('security::filament/resources/role.notification.system-role-delete.title'))
                            ->body(__('security::filament/resources/role.notification.system-role-delete.body'))
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $allPermissions = RoleResource::getAllFormPermissions();

        $rolePermissions = $this->record->permissions()->pluck('name');

        $data['select_all'] = $allPermissions->diff($rolePermissions)->isEmpty();
        $data['permissions_sync_mode'] = $data['select_all'] ? 'all' : 'manual';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $mode = $data['permissions_sync_mode'] ?? 'manual';

        if ($mode === 'all' || ($data['select_all'] ?? false)) {
            $this->permissions = RoleResource::getAllFormPermissions();
        } elseif ($mode === 'none') {
            $this->permissions = collect();
        } else {
            $this->permissions = collect($data)
                ->filter(fn ($permission, $key) => ! in_array($key, ['name', 'guard_name', 'permissions_sync_mode', 'select_all'], true))
                ->values()
                ->flatten()
                ->unique();
        }

        return [
            'name'       => $data['name'],
            'guard_name' => $data['guard_name'] ?? Utils::getFilamentAuthGuard(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->syncPermissionsByNames($this->permissions);
        $this->permissions = collect();
        $this->compactFormData();
    }

    protected function compactFormData(): void
    {
        $teamKey = config('permission.column_names.team_foreign_key');

        $this->data = collect($this->data)
            ->only(array_filter([
                'name',
                'guard_name',
                'select_all',
                'permissions_sync_mode',
                $teamKey,
            ]))
            ->all();
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('security::filament/resources/role/pages/edit-role.notification.title'))
            ->body(__('security::filament/resources/role/pages/edit-role.notification.body'));
    }
}
