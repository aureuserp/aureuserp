<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlans\Schemas;

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
                Section::make(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.entries.name'))
                            ->icon('heroicon-o-briefcase')
                            ->placeholder('—'),
                        TextEntry::make('department.name')
                            ->icon('heroicon-o-building-office-2')
                            ->placeholder('—')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.entries.department')),
                        TextEntry::make('department.manager.name')
                            ->icon('heroicon-o-user')
                            ->placeholder('—')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.entries.manager')),
                        TextEntry::make('company.name')
                            ->icon('heroicon-o-building-office')
                            ->placeholder('—')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.entries.company')),
                        IconEntry::make('is_active')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.entries.status'))
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }
}
