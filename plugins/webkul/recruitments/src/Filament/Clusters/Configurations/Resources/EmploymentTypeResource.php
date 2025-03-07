<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypeResource as BaseEmploymentTypeResource;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\EmploymentTypeResource\Pages;
use Webkul\Recruitment\Models\EmploymentType;

class EmploymentTypeResource extends BaseEmploymentTypeResource
{
    protected static ?string $model = EmploymentType::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/employment-type.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmploymentTypes::route('/'),
        ];
    }
}
