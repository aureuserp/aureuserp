<?php

namespace Webkul\Employee\Filament\Resources\DepartmentResource\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Employee\Models\Department;

class DepartmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/resources/department.infolist.sections.general.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office-2')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.name')),
                                        TextEntry::make('manager.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.manager')),
                                        TextEntry::make('company.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.company')),
                                        ColorEntry::make('color')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.color')),
                                        Fieldset::make(__('employees::filament/resources/department.infolist.sections.general.entries.hierarchy-title'))
                                            ->hidden(fn (Department $record): bool => $record->parent === null)
                                            ->schema([
                                                TextEntry::make('hierarchy')
                                                    ->label('')
                                                    ->html()
                                                    ->state(fn (Department $record): string => static::buildHierarchyTree($record)),
                                            ])->columnSpan('full'),
                                    ])
                                    ->columns(2)->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    private static function buildHierarchyTree(Department $currentDepartment): string
    {
        $rootDepartment = static::findRootDepartment($currentDepartment);

        return static::renderDepartmentTree($rootDepartment, $currentDepartment);
    }

    private static function findRootDepartment(Department $department): Department
    {
        $current = $department;

        while ($current->parent_id) {
            $current = $current->parent;
        }

        return $current;
    }

    private static function renderDepartmentTree(
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
            ->when($department->company_id, fn ($query, $companyId) => $query->where(owned_by_company($companyId)))
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

    private static function formatDepartmentLine(
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
}
