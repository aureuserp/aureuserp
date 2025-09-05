<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Stages\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.general-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-cube')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.general-information.entries.stage-name')),
                                        TextEntry::make('sort')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-bars-3-bottom-right')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.general-information.entries.sort')),
                                        TextEntry::make('requirements')
                                            ->icon('heroicon-o-document-text')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.general-information.entries.requirements'))
                                            ->placeholder('—')
                                            ->html()
                                            ->columnSpanFull(),
                                    ])->columns(2),
                                Section::make(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.additional-information.title'))
                                    ->schema([
                                        TextEntry::make('jobs.name')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.additional-information.entries.job-positions'))
                                            ->badge()
                                            ->listWithLineBreaks()
                                            ->placeholder('—'),
                                        IconEntry::make('fold')
                                            ->boolean()
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.additional-information.entries.folded')),
                                        IconEntry::make('hired_stage')
                                            ->boolean()
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.additional-information.entries.hired-stage')),
                                        IconEntry::make('is_default')
                                            ->boolean()
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.additional-information.entries.default-stage')),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.tooltips.title'))
                                ->schema([
                                    TextEntry::make('legend_normal')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.tooltips.entries.gray-label'))
                                        ->icon('heroicon-o-information-circle')
                                        ->placeholder('—'),
                                    TextEntry::make('legend_blocked')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.tooltips.entries.red-label'))
                                        ->icon('heroicon-o-x-circle')
                                        ->iconColor('danger')
                                        ->placeholder('—'),
                                    TextEntry::make('legend_done')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.tooltips.entries.green-label'))
                                        ->icon('heroicon-o-check-circle')
                                        ->iconColor('success')
                                        ->placeholder('—'),
                                ]),

                        ])->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }
}
