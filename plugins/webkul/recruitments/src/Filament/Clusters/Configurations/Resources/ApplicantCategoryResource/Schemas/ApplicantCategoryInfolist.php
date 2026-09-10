<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategoryResource\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ApplicantCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/applicant-category.infolist.name')),
                ColorEntry::make('color')
                    ->placeholder('—')
                    ->label(__('recruitments::filament/clusters/configurations/resources/applicant-category.infolist.color')),
            ]);
    }
}
