<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Pages\ViewJobPosition as BaseViewJobPosition;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\JobPositionResource;

class ViewJobPosition extends BaseViewJobPosition
{
    protected static string $resource = JobPositionResource::class;
}
