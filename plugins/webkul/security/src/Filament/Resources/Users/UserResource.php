<?php

namespace Webkul\Security\Filament\Resources\Users;

use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Filament\Resources\Users\Pages\CreateUser;
use Webkul\Security\Filament\Resources\Users\Pages\EditUser;
use Webkul\Security\Filament\Resources\Users\Pages\ListUsers;
use Webkul\Security\Filament\Resources\Users\Pages\ViewUsers;
use Webkul\Security\Filament\Resources\Users\Schemas\UserForm;
use Webkul\Security\Filament\Resources\Users\Schemas\UserInfolist;
use Webkul\Security\Filament\Resources\Users\Tables\UsersTable;
use Webkul\Security\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'users';

    public static function getSlug(?Panel $panel = null): string
    {
        return static::$slug ?? parent::getSlug($panel);
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/user.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('security::filament/resources/user.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('security::filament/resources/user.global-search.name')  => $record->name ?? '—',
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
