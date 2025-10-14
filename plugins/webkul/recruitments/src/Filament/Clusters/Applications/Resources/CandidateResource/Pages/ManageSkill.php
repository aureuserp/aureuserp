<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource;
use Webkul\Recruitment\Traits\CandidateSkillRelation;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageSkill extends ManageRelatedRecords
{
    use CandidateSkillRelation, HasRecordNavigationTabs;

    protected static string $resource = CandidateResource::class;

    protected static string $relationship = 'skills';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/employee/pages/manage-skill.navigation.title');
    }
}
