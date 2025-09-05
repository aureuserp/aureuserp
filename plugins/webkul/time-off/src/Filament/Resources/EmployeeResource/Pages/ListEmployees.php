<?php

namespace Webkul\TimeOff\Filament\Resources\EmployeeResource\Pages;

use Webkul\TimeOff\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ListEmployees as BaseListEmployees;

class ListEmployees extends BaseListEmployees
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
