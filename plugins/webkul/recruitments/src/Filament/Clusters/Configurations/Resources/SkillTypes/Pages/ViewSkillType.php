<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypes\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypes\Pages\ViewSkillType as ViewSkillTypeBase;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypes\SkillTypeResource;

class ViewSkillType extends ViewSkillTypeBase
{
    protected static string $resource = SkillTypeResource::class;
}
