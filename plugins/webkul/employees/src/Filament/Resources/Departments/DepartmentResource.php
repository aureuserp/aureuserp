<?php

namespace Webkul\Employee\Filament\Resources\Departments;

use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Filament\Resources\Departments\Pages\CreateDepartment;
use Webkul\Employee\Filament\Resources\Departments\Pages\EditDepartment;
use Webkul\Employee\Filament\Resources\Departments\Pages\ListDepartments;
use Webkul\Employee\Filament\Resources\Departments\Pages\ViewDepartment;
use Webkul\Employee\Filament\Resources\Departments\Schemas\DepartmentForm;
use Webkul\Employee\Filament\Resources\Departments\Schemas\DepartmentInfolist;
use Webkul\Employee\Filament\Resources\Departments\Tables\DepartmentsTable;
use Webkul\Employee\Models\Department;
use Webkul\Field\Filament\Traits\HasCustomFields;

class DepartmentResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Department::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/department.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/resources/department.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'manager.name', 'company.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('employees::filament/resources/department.global-search.name')               => $record->name ?? '—',
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

    public static function buildHierarchyTree(Department $currentDepartment): string
    {
        $rootDepartment = static::findRootDepartment($currentDepartment);

        return static::renderDepartmentTree($rootDepartment, $currentDepartment);
    }

    protected static function findRootDepartment(Department $department): Department
    {
        $current = $department;
        while ($current->parent_id) {
            $current = $current->parent;
        }

        return $current;
    }

    protected static function renderDepartmentTree(
        Department $department,
        Department $currentDepartment,
        int $depth = 0,
        bool $isLast = true,
        array $parentIsLast = []
    ): string {
        $output = static::formatDepartmentLine(
            $department,
            $depth,
            $department->id === $currentDepartment->id,
            $isLast,
            $parentIsLast
        );

        $children = Department::where('parent_id', $department->id)
            ->where('company_id', $department->company_id)
            ->orderBy('name')
            ->get();

        if ($children->isNotEmpty()) {
            $lastIndex = $children->count() - 1;

            foreach ($children as $index => $child) {
                $newParentIsLast = array_merge($parentIsLast, [$isLast]);

                $output .= static::renderDepartmentTree(
                    $child,
                    $currentDepartment,
                    $depth + 1,
                    $index === $lastIndex,
                    $newParentIsLast
                );
            }
        }

        return $output;
    }

    protected static function formatDepartmentLine(
        Department $department,
        int $depth,
        bool $isActive,
        bool $isLast,
        array $parentIsLast
    ): string {
        $prefix = '';
        if ($depth > 0) {
            for ($i = 0; $i < $depth - 1; $i++) {
                $prefix .= $parentIsLast[$i] ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '&nbsp;&nbsp;&nbsp;';
            }
            $prefix .= $isLast ? '└──&nbsp;' : '├──&nbsp;';
        }

        $employeeCount = $department->employees()->count();
        $managerName = $department->manager?->name ? " · {$department->manager->name}" : '';

        $style = $isActive
            ? 'color: '.($department->color ?? '#1D4ED8').'; font-weight: bold;'
            : '';

        return sprintf(
            '<div class="py-1" style="%s">
                <span class="inline-flex items-center gap-2">
                    %s%s%s
                    <span class="text-sm text-gray-500">
                        (%d members)
                    </span>
                </span>
            </div>',
            $style,
            $prefix,
            e($department->name),
            e($managerName),
            $employeeCount
        );
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'employees/departments';
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'view'   => ViewDepartment::route('/{record}'),
            'edit'   => EditDepartment::route('/{record}/edit'),
        ];
    }
}
