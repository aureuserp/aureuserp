<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Support\Enums\ActivityDelayInterval;

class ActivityTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.activity-details.title'))
                                    ->schema([
                                        TextEntry::make('activityType.name')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.activity-details.entries.activity-type'))
                                            ->placeholder('—')
                                            ->icon('heroicon-o-briefcase'),
                                        TextEntry::make('summary')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.activity-details.entries.summary'))
                                            ->placeholder('—')
                                            ->icon('heroicon-o-document-text'),
                                    ])->columns(2),
                                Section::make(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.delay-information.title'))
                                    ->schema([
                                        TextEntry::make('delay_count')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.delay-information.entries.delay-count'))
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock'),
                                        TextEntry::make('delay_unit')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.delay-information.entries.delay-unit'))
                                            ->placeholder('—')
                                            ->icon('heroicon-o-calendar'),
                                        TextEntry::make('delay_from')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.delay-information.entries.delay-from'))
                                            ->placeholder('—')
                                            ->formatStateUsing(fn ($state) => ActivityDelayInterval::options()[$state])
                                            ->helperText(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.delay-information.entries.delay-from-helper-text'))
                                            ->icon('heroicon-o-ellipsis-horizontal-circle'),
                                    ])->columns(2),
                                TextEntry::make('note')
                                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.note'))
                                    ->html()
                                    ->placeholder('—')
                                    ->icon('heroicon-o-document'),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.assignment.title'))
                                ->schema([
                                    TextEntry::make('responsible_type')
                                        ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.assignment.entries.assignment'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-user-circle'),
                                    TextEntry::make('responsible.name')
                                        ->placeholder('—')
                                        ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.infolist.sections.assignment.entries.assignee'))
                                        ->icon('heroicon-o-user'),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ])
            ->columns(1);
    }
}
