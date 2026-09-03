<?php

namespace Webkul\Security\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Webkul\Security\Filament\Resources\UserResource\Pages\CreateUser;
use Webkul\Security\Filament\Resources\UserResource\Pages\EditUser;
use Webkul\Security\Filament\Resources\UserResource\Pages\ListUsers;
use Webkul\Security\Filament\Resources\UserResource\Pages\ViewUsers;
use Webkul\Security\Filament\Resources\UserResource\Schemas\UserForm;
use Webkul\Security\Filament\Resources\UserResource\Schemas\UserInfolist;
use Webkul\Security\Filament\Resources\UserResource\Tables\UsersTable;
use Webkul\Security\Models\User;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Enums\NavigationGroup;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->ownership();
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/user.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('security::filament/resources/user.global-search.email') => $record->email ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function canDeleteUser(User $record): bool
    {
        return ! $record->is_default && $record->id !== Auth::id();
    }

    public static function ensureAdminRoleConstraints(?User $record, array $roleIds): void
    {
        $adminRoleIds = self::getProtectedAdminRoleIds();

        if ($adminRoleIds === []) {
            return;
        }

        $normalizedRoleIds = array_map('intval', $roleIds);
        $isAdminCurrently = $record?->roles()->whereKey($adminRoleIds)->exists() ?? false;
        $willBeAdmin = count(array_intersect($adminRoleIds, $normalizedRoleIds)) > 0;
        $adminUsersCount = User::query()
            ->whereHas('roles', fn (Builder $query) => $query->whereKey($adminRoleIds))
            ->count();

        $firstUserId = User::min('id');
        $isFirstUser = $record && $record->id === $firstUserId;

        if (
            $isFirstUser
            && ! $willBeAdmin
        ) {
            throw ValidationException::withMessages([
                'roles' => __('security::filament/resources/user.form.validation.first-user-must-be-admin'),
            ]);
        }

        if (
            $adminUsersCount === 1
            && $isAdminCurrently
            && ! $willBeAdmin
        ) {
            throw ValidationException::withMessages([
                'roles' => __('security::filament/resources/user.form.validation.cannot-remove-last-admin'),
            ]);
        }
    }

    protected static function getProtectedAdminRoleIds(): array
    {
        $defaultRoleId = settings(UserSettings::class)->default_role_id;

        $candidateNames = array_values(array_filter([
            config('filament-shield.panel_user.name'),
            config('filament-shield.super_admin.name'),
            'admin',
            'Admin',
            'panel_user',
            'super_admin',
        ]));

        $normalizedCandidateNames = array_unique(
            array_map(static fn (string $name): string => Str::lower(trim($name)), $candidateNames)
        );

        $roleIdsFromNames = Role::query()
            ->get(['id', 'name'])
            ->filter(function (Role $role) use ($normalizedCandidateNames) {
                $normalizedRoleName = Str::lower($role->name);

                return in_array($normalizedRoleName, $normalizedCandidateNames, true)
                    || str_contains($normalizedRoleName, 'admin');
            })
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->values()
            ->all();

        return collect($roleIdsFromNames)
            ->when($defaultRoleId, fn ($collection) => $collection->push((int) $defaultRoleId))
            ->unique()
            ->values()
            ->all();
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
            'view'   => ViewUsers::route('/{record}'),
        ];
    }
}
