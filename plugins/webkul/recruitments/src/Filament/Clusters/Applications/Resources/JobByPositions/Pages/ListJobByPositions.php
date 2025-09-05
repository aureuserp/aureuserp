<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositions\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Pages\ListJobPositions as JobPositionResource;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositions\JobByPositionResource;

class ListJobByPositions extends JobPositionResource
{
    protected static string $resource = JobByPositionResource::class;

    public function getHeaderActions(): array
    {
        return [];
    }
}
