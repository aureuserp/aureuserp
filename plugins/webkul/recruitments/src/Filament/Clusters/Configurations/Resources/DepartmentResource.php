<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Resources\DepartmentResource as BaseDepartmentResource;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource\Pages\CreateDepartment;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource\Pages\EditDepartment;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource\Pages\ListDepartments;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource\Pages\ManageEmployee;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource\Pages\ViewDepartment;
use Webkul\Recruitment\Models\Department;

class DepartmentResource extends BaseDepartmentResource
{
    protected static ?string $model = Department::class;

    protected static ?string $cluster = Configurations::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/department.navigation.group');
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'departments';
    }

    public static function table(Table $table): Table
    {
        return BaseDepartmentResource::table($table)
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ]);
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
            'edit'       => EditDepartment::route('/{record}/edit'),
            'view'       => ViewDepartment::route('/{record}'),
            'employees'  => ManageEmployee::route('/{record}/employees'),
        ];
    }
}
