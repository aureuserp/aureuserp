<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('projects::filament/clusters/configurations/resources/activity-plan.infolist.name'))
                            ->icon('heroicon-o-briefcase')
                            ->placeholder('—'),
                        IconEntry::make('is_active')
                            ->label(__('projects::filament/clusters/configurations/resources/activity-plan.infolist.status'))
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }
}
