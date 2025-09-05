<?php

namespace Webkul\Project\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Partner\Filament\Resources\Partners\PartnerResource;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\Projects\ProjectResource;
use Webkul\Project\Filament\Resources\Tasks\TaskResource;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\TaskStage;
use Webkul\Project\Settings\TaskSettings;
use Webkul\Project\Settings\TimeSettings;
use Webkul\Security\Filament\Resources\Users\UserResource;

class TaskForm
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
                        ProgressStepper::make('stage_id')
                            ->hiddenLabel()
                            ->inline()
                            ->required()
                            ->options(fn () => TaskStage::orderBy('sort')->get()->mapWithKeys(fn ($stage) => [$stage->id => $stage->name]))
                            ->default(TaskStage::first()?->id),
                        Section::make(__('projects::filament/resources/task.form.sections.general.title'))
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('projects::filament/resources/task.form.sections.general.fields.title'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('projects::filament/resources/task.form.sections.general.fields.title-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                ToggleButtons::make('state')
                                    ->required()
                                    ->default(TaskState::IN_PROGRESS)
                                    ->inline()
                                    ->options(TaskState::options())
                                    ->colors(TaskState::colors())
                                    ->icons(TaskState::icons()),
                                Select::make('tags')
                                    ->label(__('projects::filament/resources/task.form.sections.general.fields.tags'))
                                    ->relationship(name: 'tags', titleAttribute: 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label(__('projects::filament/resources/task.form.sections.general.fields.name'))
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique('projects_tags'),
                                                ColorPicker::make('color')
                                                    ->default('#808080')
                                                    ->hexColor()
                                                    ->label(__('projects::filament/resources/task.form.sections.general.fields.color')),
                                            ])->columns(2),
                                    ]),
                                RichEditor::make('description')
                                    ->label(__('projects::filament/resources/task.form.sections.general.fields.description')),
                            ]),

                        Section::make(__('projects::filament/resources/task.form.sections.additional.title'))
                            ->visible(! empty($customFormFields = static::getCustomFormFields()))
                            ->schema($customFormFields),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('projects::filament/resources/task.form.sections.settings.title'))
                            ->schema([
                                Select::make('project_id')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.project'))
                                    ->relationship('project', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->createOptionForm(fn (Schema $schema): Schema => ProjectResource::form($schema))
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('milestone_id', null);
                                    }),
                                Select::make('milestone_id')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.milestone'))
                                    ->relationship(
                                        name: 'milestone',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('project_id', $get('project_id')),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('projects::filament/resources/task.form.sections.settings.fields.milestone-hint-text'))
                                    ->createOptionForm(fn ($get) => [
                                        TextInput::make('name')
                                            ->label(__('projects::filament/resources/task.form.sections.settings.fields.name'))
                                            ->required()
                                            ->maxLength(255),
                                        DateTimePicker::make('deadline')
                                            ->label(__('projects::filament/resources/task.form.sections.settings.fields.deadline'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-clock'),
                                        Toggle::make('is_completed')
                                            ->label(__('projects::filament/resources/task.form.sections.settings.fields.is-completed'))
                                            ->required(),
                                        Hidden::make('project_id')
                                            ->default($get('project_id')),
                                        Hidden::make('creator_id')
                                            ->default(fn () => Auth::user()->id),
                                    ])
                                    ->hidden(function (TaskSettings $taskSettings, Get $get) {
                                        $project = Project::find($get('project_id'));

                                        if (! $project) {
                                            return true;
                                        }

                                        if (! $taskSettings->enable_milestones) {
                                            return true;
                                        }

                                        return ! $project->allow_milestones;
                                    })
                                    ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_milestones),
                                Select::make('partner_id')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.customer'))
                                    ->relationship('partner', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                                    ->editOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema)),
                                Select::make('users')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.assignees'))
                                    ->relationship('users', 'name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => UserResource::form($schema)),
                                DateTimePicker::make('deadline')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.deadline'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar'),
                                TextInput::make('allocated_hours')
                                    ->label(__('projects::filament/resources/task.form.sections.settings.fields.allocated-hours'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->suffixIcon('heroicon-o-clock')
                                    ->helperText(__('projects::filament/resources/task.form.sections.settings.fields.allocated-hours-helper-text'))
                                    ->dehydrateStateUsing(fn ($state) => $state ?: 0)
                                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                            ]),
                    ]),
            ])
            ->columns(3);
    }
}
