<?php

namespace Webkul\Employee\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\CreateDepartment;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\EditDepartment;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\ListDepartments;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\ManageEmployee;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\ViewDepartment;
use Webkul\Employee\Filament\Resources\DepartmentResource\Schemas\DepartmentForm;
use Webkul\Employee\Filament\Resources\DepartmentResource\Schemas\DepartmentInfolist;
use Webkul\Employee\Filament\Resources\DepartmentResource\Tables\DepartmentsTable;
use Webkul\Employee\Models\Department;
use Webkul\Support\Enums\NavigationGroup;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/department.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'manager.name', 'company.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('employees::filament/resources/department.global-search.department-manager') => $record->manager?->name ?? '—',
            __('employees::filament/resources/department.global-search.company')            => $record->company?->name ?? '—',
        ];
    }

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DepartmentInfolist::configure($schema);
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'employees/departments';
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewDepartment::class,
            EditDepartment::class,
            ManageEmployee::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListDepartments::route('/'),
            'create'     => CreateDepartment::route('/create'),
            'view'       => ViewDepartment::route('/{record}'),
            'edit'       => EditDepartment::route('/{record}/edit'),
            'employees'  => ManageEmployee::route('/{record}/employees'),

        ];
    }
}
