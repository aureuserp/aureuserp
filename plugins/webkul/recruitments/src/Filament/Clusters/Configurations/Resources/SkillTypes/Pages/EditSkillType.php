<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypes\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypes\Pages\EditSkillType as EditSkillTypeBase;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypes\SkillTypeResource;

class EditSkillType extends EditSkillTypeBase
{
    protected static string $resource = SkillTypeResource::class;
}
