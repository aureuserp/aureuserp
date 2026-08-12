<?php

namespace Webkul\Security\Filament\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Collection;
use Webkul\Security\Filament\Resources\RoleResource;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected ?bool $hasUnsavedDataChangesAlert = false;

    protected Collection $permissions;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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

    protected function afterCreate(): void
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

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('security::filament/resources/role/pages/create-role.notification.title'))
            ->body(__('security::filament/resources/role/pages/create-role.notification.body'));
    }
}
