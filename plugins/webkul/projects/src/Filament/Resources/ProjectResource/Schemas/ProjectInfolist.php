<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Project\Enums\ProjectVisibility;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageMilestones;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageTasks;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\ProjectStage;

class ProjectInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        InfolistProgressStepper::make('stage_id')
                            ->hiddenLabel()
                            ->inline()
                            ->visible(ProjectResource::getTaskSettings()->enable_project_stages)
                            ->options(fn () => ProjectStage::orderBy('sort')->get()->mapWithKeys(fn ($stage) => [$stage->id => $stage->name])->toArray())
                            ->default(ProjectStage::first()?->id),

                        Section::make(__('projects::filament/resources/project.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('projects::filament/resources/project.infolist.sections.general.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('description')
                                    ->label(__('projects::filament/resources/project.infolist.sections.general.entries.description'))
                                    ->markdown(),
                            ]),

                        Section::make(__('projects::filament/resources/project.infolist.sections.additional.title'))
                            ->schema(array_merge([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.project-manager'))
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—'),

                                        TextEntry::make('partner.name')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.customer'))
                                            ->icon('heroicon-o-phone')
                                            ->placeholder('—'),

                                        TextEntry::make('planned_date')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.project-timeline'))
                                            ->icon('heroicon-o-calendar')
                                            ->state(function (Project $record): ?string {
                                                if (! $record->start_date || ! $record->end_date) {
                                                    return '—';
                                                }

                                                return $record->start_date->format('d M Y').' - '.$record->end_date->format('d M Y');
                                            }),

                                        TextEntry::make('allocated_hours')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.allocated-hours'))
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—')
                                            ->suffix(__('projects::filament/resources/project.infolist.sections.additional.entries.allocated-hours-suffix'))
                                            ->visible(ProjectResource::getTimeSettings()->enable_timesheets),

                                        TextEntry::make('remaining_hours')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.remaining-hours'))
                                            ->icon('heroicon-o-clock')
                                            ->suffix(__('projects::filament/resources/project.infolist.sections.additional.entries.remaining-hours-suffix'))
                                            ->color(fn (Project $record): string => $record->remaining_hours < 0 ? 'danger' : 'success')
                                            ->visible(ProjectResource::getTimeSettings()->enable_timesheets),

                                        TextEntry::make('stage.name')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.current-stage'))
                                            ->icon('heroicon-o-flag')
                                            ->badge()
                                            ->visible(ProjectResource::getTaskSettings()->enable_project_stages),

                                        TextEntry::make('tags.name')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.tags'))
                                            ->badge()
                                            ->state(function (Project $record): array {
                                                return $record->tags->map(fn ($tag) => [
                                                    'label' => $tag->name,
                                                    'color' => $tag->color ?? '#808080',
                                                ])->toArray();
                                            })
                                            ->badge()
                                            ->formatStateUsing(fn ($state) => $state['label'])
                                            ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                            ->listWithLineBreaks()
                                            ->separator(', ')
                                            ->weight(FontWeight::Bold),
                                    ]),
                            ], $customInfolistEntries)),

                        Section::make(__('projects::filament/resources/project.infolist.sections.statistics.title'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('tasks_count')
                                            ->label(__('projects::filament/resources/project.infolist.sections.statistics.entries.total-tasks'))
                                            ->state(fn (Project $record): int => $record->tasks()->count())
                                            ->icon('heroicon-m-clipboard-document-list')
                                            ->iconColor('primary')
                                            ->color('primary')
                                            ->url(fn (Project $record): string => ManageTasks::getUrl(['record' => $record])),

                                        TextEntry::make('milestones_completion')
                                            ->label(__('projects::filament/resources/project.infolist.sections.statistics.entries.milestones-progress'))
                                            ->state(function (Project $record): string {
                                                $completed = $record->milestones()->where('is_completed', true)->count();
                                                $total = $record->milestones()->count();

                                                return "{$completed}/{$total}";
                                            })
                                            ->icon('heroicon-m-flag')
                                            ->iconColor('primary')
                                            ->color('primary')
                                            ->url(fn (Project $record): string => ManageMilestones::getUrl(['record' => $record]))
                                            ->visible(fn (Project $record) => ProjectResource::getTaskSettings()->enable_milestones && $record->allow_milestones),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('projects::filament/resources/project.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('projects::filament/resources/project.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('projects::filament/resources/project.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('projects::filament/resources/project.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),

                        Section::make(__('projects::filament/resources/project.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('visibility')
                                    ->label(__('projects::filament/resources/project.infolist.sections.settings.entries.visibility'))
                                    ->badge()
                                    ->icon(fn (string $state): string => ProjectVisibility::icons()[$state])
                                    ->color(fn (string $state): string => ProjectVisibility::colors()[$state])
                                    ->formatStateUsing(fn (string $state): string => ProjectVisibility::options()[$state]),

                                IconEntry::make('allow_timesheets')
                                    ->label(__('projects::filament/resources/project.infolist.sections.settings.entries.timesheets-enabled'))
                                    ->boolean()
                                    ->visible(ProjectResource::getTimeSettings()->enable_timesheets),

                                IconEntry::make('allow_milestones')
                                    ->label(__('projects::filament/resources/project.infolist.sections.settings.entries.milestones-enabled'))
                                    ->boolean()
                                    ->visible(ProjectResource::getTaskSettings()->enable_milestones),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
