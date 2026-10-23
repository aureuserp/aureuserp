<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource\Pages\CreateUser;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource\Pages\EditUser;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource\Pages\ListUsers;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource\Pages\ViewUser;
use Webkul\Crm\Filament\Concerns\HasCrmAuthorization;
use Webkul\Crm\Policies\UserPolicy;
use Webkul\Security\Filament\Resources\UserResource as BaseUserResource;

class UserResource extends BaseUserResource
{
    use HasCrmAuthorization;

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 7;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static bool $isGloballySearchable = false;

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/user.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/user.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/user.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/user.navigation.group');
    }

    public static function getCrmPolicy(): string
    {
        return UserPolicy::class;
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view'   => ViewUser::route('/{record}'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}
