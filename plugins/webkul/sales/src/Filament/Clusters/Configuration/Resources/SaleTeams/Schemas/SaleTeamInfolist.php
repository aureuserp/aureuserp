<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SaleTeamInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.name'))
                                    ->columnSpan(1),
                                Fieldset::make(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.title'))
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.team-leader'))
                                            ->icon('heroicon-o-user'),
                                        TextEntry::make('company.name')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                        TextEntry::make('invoiced_target')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.invoiced-target'))
                                            ->suffix(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.invoiced-target-suffix'))
                                            ->numeric(),
                                        ColorEntry::make('color')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.color')),
                                        TextEntry::make('members.name')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.members'))
                                            ->listWithLineBreaks()
                                            ->bulleted(),
                                    ])
                                    ->columns(2),
                                IconEntry::make('is_active')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.status'))
                                    ->boolean(),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }
}
