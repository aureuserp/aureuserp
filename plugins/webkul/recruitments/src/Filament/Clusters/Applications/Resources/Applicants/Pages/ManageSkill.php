<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Pages;

use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\ApplicantResource;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Pages\ManageSkill as BaseManageSkill;

class ManageSkill extends BaseManageSkill
{
    protected static string $resource = ApplicantResource::class;
}
