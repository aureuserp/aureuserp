<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Pages\CreateJobPosition as BaseCreateJobPosition;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\JobPositionResource;

class CreateJobPosition extends BaseCreateJobPosition
{
    protected static string $resource = JobPositionResource::class;
}
