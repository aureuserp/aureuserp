<?php

namespace Webkul\Security\Filament\Resources\Roles;

use BezhanSalleh\FilamentShield\Resources\RoleResource as BaseRoleResource;
use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Security\Filament\Resources\Roles\Pages\CreateRole;
use Webkul\Security\Filament\Resources\Roles\Pages\EditRole;
use Webkul\Security\Filament\Resources\Roles\Pages\ListRoles;
use Webkul\Security\Filament\Resources\Roles\Pages\ViewRole;
use Webkul\Security\Filament\Resources\Roles\Schemas\RoleForm;
use Webkul\Security\Filament\Resources\Roles\Tables\RolesTable;

class RoleResource extends BaseRoleResource
{
    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'roles';

    public static function getSlug(?Panel $panel = null): string
    {
        return static::$slug ?? parent::getSlug($panel);
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view'   => ViewRole::route('/{record}'),
            'edit'   => EditRole::route('/{record}/edit'),
        ];
    }
}
