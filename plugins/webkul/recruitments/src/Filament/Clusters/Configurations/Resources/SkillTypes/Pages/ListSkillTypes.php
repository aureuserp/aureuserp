<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypes\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypes\Pages\ListSkillTypes as ListSkillTypesBase;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypes\SkillTypeResource;

class ListSkillTypes extends ListSkillTypesBase
{
    protected static string $resource = SkillTypeResource::class;
}
