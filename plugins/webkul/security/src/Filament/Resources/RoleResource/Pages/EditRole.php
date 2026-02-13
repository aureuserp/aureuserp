<?php

namespace Webkul\Security\Filament\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Webkul\Security\Filament\Resources\RoleResource;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public Collection $permissions;

    protected function getActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (Model $record) => $record->name == config('filament-shield.panel_user.name')),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(fn ($permission, $key) => ! in_array($key, ['name', 'guard_name', 'select_all']))
            ->values()
            ->flatten()
            ->unique();

        return [
            'name'       => $data['name'],
            'guard_name' => $data['guard_name'] ?? Utils::getFilamentAuthGuard(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->syncPermissionsByNames($this->permissions);
    }
}
