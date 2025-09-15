<?php

namespace Webkul\Employee\Traits\Resources\Department;

use Filament\Actions\CreateAction;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Resources\EmployeeResource;

trait DepartmentEmployee
{
    public function table(Table $table): Table
    {
        $table = EmployeeResource::table($table);

        [$view, $edit] = $table->getActions();
        
        $view = $view->url(fn ($record) => EmployeeResource::getUrl('view', ['record' => $record]))
            ->openUrlInNewTab(false);
        $edit = $edit->url(fn ($record) => EmployeeResource::getUrl('edit', ['record' => $record]))
            ->openUrlInNewTab(false);

        $table->headerActions([
            CreateAction::make()
                ->url(fn () => EmployeeResource::getUrl('create'))
                ->openUrlInNewTab(false)
                ->icon('heroicon-o-plus-circle')
                ->label(__('employees::filament/resources/employee/pages/list-employee.header-actions.create.label')),
        ]);

        return $table;
    }
}
