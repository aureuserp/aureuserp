<?php

namespace Webkul\Project\Filament\Resources\Tasks\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\Projects\ProjectResource;
use Webkul\Project\Filament\Resources\Tasks\TaskResource;
use Webkul\Project\Models\Task;
use Webkul\Project\Settings\TaskSettings;
use Webkul\Project\Settings\TimeSettings;

class TaskInfolist
{
    use HasCustomFields;

    public static function getModel(): string
    {
        return TaskResource::class;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('projects::filament/resources/task.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('title')
                                    ->label(__('projects::filament/resources/task.infolist.sections.general.entries.title'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('state')
                                    ->label(__('projects::filament/resources/task.infolist.sections.general.entries.state'))
                                    ->badge()
                                    ->icon(fn (string $state): string => TaskState::icons()[$state])
                                    ->color(fn (string $state): string => TaskState::colors()[$state])
                                    ->formatStateUsing(fn (string $state): string => TaskState::options()[$state]),

                                IconEntry::make('priority')
                                    ->label(__('projects::filament/resources/task.infolist.sections.general.entries.priority'))
                                    ->icon(fn ($record): string => $record->priority ? 'heroicon-s-star' : 'heroicon-o-star')
                                    ->color(fn ($record): string => $record->priority ? 'warning' : 'gray'),

                                TextEntry::make('description')
                                    ->label(__('projects::filament/resources/task.infolist.sections.general.entries.description'))
                                    ->html(),

                                TextEntry::make('tags.name')
                                    ->label(__('projects::filament/resources/task.infolist.sections.general.entries.tags'))
                                    ->badge()
                                    ->state(function (Task $record): array {
                                        return $record->tags()->get()->map(fn ($tag) => [
                                            'label' => $tag->name,
                                            'color' => $tag->color ?? '#808080',
                                        ])->toArray();
                                    })
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state['label'])
                                    ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                    ->listWithLineBreaks()
                                    ->separator(', '),
                            ]),

                        Section::make(__('projects::filament/resources/task.infolist.sections.project-information.title'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('project.name')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.project'))
                                            ->icon('heroicon-o-folder')
                                            ->placeholder('—')
                                            ->color('primary')
                                            ->url(fn (Task $record): string => $record->project_id ? ProjectResource::getUrl('view', ['record' => $record->project]) : '#'),

                                        TextEntry::make('milestone.name')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.milestone'))
                                            ->icon('heroicon-o-flag')
                                            ->placeholder('—')
                                            ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_milestones),

                                        TextEntry::make('stage.name')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.stage'))
                                            ->icon('heroicon-o-queue-list')
                                            ->badge(),

                                        TextEntry::make('partner.name')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.customer'))
                                            ->icon('heroicon-o-queue-list')
                                            ->icon('heroicon-o-phone')
                                            ->listWithLineBreaks()
                                            ->placeholder('—'),

                                        TextEntry::make('users.name')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.assignees'))
                                            ->icon('heroicon-o-users')
                                            ->listWithLineBreaks()
                                            ->placeholder('—'),

                                        TextEntry::make('deadline')
                                            ->label(__('projects::filament/resources/task.infolist.sections.project-information.entries.deadline'))
                                            ->icon('heroicon-o-calendar')
                                            ->dateTime()
                                            ->placeholder('—'),
                                    ]),
                            ]),

                        Section::make(__('projects::filament/resources/task.infolist.sections.time-tracking.title'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('allocated_hours')
                                            ->label(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.allocated-time'))
                                            ->icon('heroicon-o-clock')
                                            ->suffix(' Hours')
                                            ->placeholder('—')
                                            ->formatStateUsing(function ($state) {
                                                $hours = floor($state);
                                                $minutes = ($state - $hours) * 60;

                                                return $hours.':'.$minutes;
                                            })
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                                        TextEntry::make('total_hours_spent')
                                            ->label(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.time-spent'))
                                            ->icon('heroicon-o-clock')
                                            ->suffix(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.time-spent-suffix'))
                                            ->formatStateUsing(function ($state) {
                                                $hours = floor($state);
                                                $minutes = ($state - $hours) * 60;

                                                return $hours.':'.$minutes;
                                            })
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                                        TextEntry::make('remaining_hours')
                                            ->label(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.time-remaining'))
                                            ->icon('heroicon-o-clock')
                                            ->suffix(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.time-remaining-suffix'))
                                            ->formatStateUsing(function ($state) {
                                                $hours = floor($state);
                                                $minutes = ($state - $hours) * 60;

                                                return $hours.':'.$minutes;
                                            })
                                            ->color(fn ($state): string => $state < 0 ? 'danger' : 'success')
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                                        TextEntry::make('progress')
                                            ->label(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.progress'))
                                            ->icon('heroicon-o-chart-bar')
                                            ->suffix('%')
                                            ->color(
                                                fn ($record): string => $record->progress > 100
                                                    ? 'danger'
                                                    : ($record->progress < 100 ? 'warning' : 'success')
                                            )
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                                    ]),
                            ])
                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                        Section::make(__('projects::filament/resources/task.infolist.sections.additional-information.title'))
                            ->visible(! empty($customInfolistEntries = static::getCustomInfolistEntries()))
                            ->schema($customInfolistEntries),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('projects::filament/resources/task.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('projects::filament/resources/task.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('projects::filament/resources/task.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('projects::filament/resources/task.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),

                        Section::make(__('projects::filament/resources/task.infolist.sections.statistics.title'))
                            ->schema([
                                TextEntry::make('subtasks_count')
                                    ->label(__('projects::filament/resources/task.infolist.sections.statistics.entries.sub-tasks'))
                                    ->state(fn (Task $record): int => $record->subTasks()->count())
                                    ->icon('heroicon-o-clipboard-document-list'),

                                TextEntry::make('timesheets_count')
                                    ->label(__('projects::filament/resources/task.infolist.sections.statistics.entries.timesheet-entries'))
                                    ->state(fn (Task $record): int => $record->timesheets()->count())
                                    ->icon('heroicon-o-clock')
                                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
