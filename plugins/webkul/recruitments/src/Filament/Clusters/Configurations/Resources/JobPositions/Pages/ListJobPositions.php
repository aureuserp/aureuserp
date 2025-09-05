<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Pages\ListJobPositions as BaseListJobPositions;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\JobPositionResource;

class ListJobPositions extends BaseListJobPositions
{
    protected static string $resource = JobPositionResource::class;
}
