<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource as BaseSkillTypeResource;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypeResource\Pages;
use Webkul\Recruitment\Models\SkillType;

class SkillTypeResource extends BaseSkillTypeResource
{
    protected static ?string $model = SkillType::class;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/skill-type.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSkillTypes::route('/'),
            'view'   => Pages\ViewSkillType::route('/{record}'),
            'edit'   => Pages\EditSkillType::route('/{record}/edit'),
        ];
    }
}
