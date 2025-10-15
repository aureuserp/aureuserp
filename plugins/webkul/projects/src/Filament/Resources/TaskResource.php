<?php

namespace Webkul\Project\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageTasks;
use Webkul\Project\Filament\Resources\TaskResource\Pages\CreateTask;
use Webkul\Project\Filament\Resources\TaskResource\Pages\EditTask;
use Webkul\Project\Filament\Resources\TaskResource\Pages\ListTasks;
use Webkul\Project\Filament\Resources\TaskResource\Pages\ManageSubTasks;
use Webkul\Project\Filament\Resources\TaskResource\Pages\ManageTimesheets;
use Webkul\Project\Filament\Resources\TaskResource\Pages\ViewTask;
use Webkul\Project\Filament\Resources\TaskResource\RelationManagers\SubTasksRelationManager;
use Webkul\Project\Filament\Resources\TaskResource\RelationManagers\TimesheetsRelationManager;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Project\Settings\TaskSettings;
use Webkul\Project\Settings\TimeSettings;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Support\Filament\Tables\Columns\ProgressBarEntry;

class TaskResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Task::class;

    protected static ?string $slug = 'project/tasks';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/task.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('projects::filament/resources/task.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'project.name', 'partner.name', 'milestone.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('projects::filament/resources/task.global-search.project')   => $record->project?->name ?? '—',
            __('projects::filament/resources/task.global-search.customer')  => $record->partner?->name ?? '—',
            __('projects::filament/resources/task.global-search.milestone') => $record->milestone?->name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
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
                                    ->relationship('project', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                    ->getOptionLabelFromRecordUsing(function (Model $record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->preload()
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
                                    ->hidden(function (Get $get) {
                                        $project = Project::find($get('project_id'));

                                        if (! $project) {
                                            return true;
                                        }

                                        if (! static::getTaskSettings()->enable_milestones) {
                                            return true;
                                        }

                                        return ! $project->allow_milestones;
                                    })
                                    ->visible(static::getTaskSettings()->enable_milestones),
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
                                    ->visible(static::getTimeSettings()->enable_timesheets),
                            ]),
                    ]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns(static::mergeCustomTableColumns([
                TextColumn::make('id')
                    ->label(__('projects::filament/resources/task.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('priority')
                    ->label(__('projects::filament/resources/task.table.columns.priority'))
                    ->icon(fn (Task $record): string => $record->priority ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Task $record): string => $record->priority ? 'warning' : 'gray')
                    ->action(function (Task $record): void {
                        $record->update([
                            'priority' => ! $record->priority,
                        ]);
                    }),
                IconColumn::make('state')
                    ->label(__('projects::filament/resources/task.table.columns.state'))
                    ->sortable()
                    ->toggleable()
                    ->icon(fn (TaskState $state): string => $state->getIcon())
                    ->color(fn (TaskState $state): string => $state->getColor())
                    ->tooltip(fn (TaskState $state): string => $state->getLabel())
                    ->action(
                        Action::make('updateState')
                            ->modalHeading('Update Task State')
                            ->schema(fn (Task $record): array => [
                                ToggleButtons::make('state')
                                    ->label(__('projects::filament/resources/task.table.columns.new-state'))
                                    ->required()
                                    ->default($record->state)
                                    ->options(TaskState::class)
                                    ->inline(),
                            ])
                            ->modalSubmitActionLabel(__('projects::filament/resources/task.table.columns.update-state'))
                            ->action(function (Task $record, array $data): void {
                                $record->update([
                                    'state' => $data['state'],
                                ]);
                            })
                    ),
                TextColumn::make('title')
                    ->label(__('projects::filament/resources/task.table.columns.title'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('project.name')
                    ->label(__('projects::filament/resources/task.table.columns.project'))
                    ->hiddenOn(ManageTasks::class)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('projects::filament/resources/task.table.columns.project-placeholder')),
                TextColumn::make('milestone.name')
                    ->label(__('projects::filament/resources/task.table.columns.milestone'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(static::getTaskSettings()->enable_milestones),
                TextColumn::make('partner.name')
                    ->label(__('projects::filament/resources/task.table.columns.customer'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('users.name')
                    ->label(__('projects::filament/resources/task.table.columns.assignees'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('allocated_hours')
                    ->label(__('projects::filament/resources/task.table.columns.allocated-time'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize(
                        Sum::make()
                            ->label(__('projects::filament/resources/task.table.columns.allocated-time'))
                            ->numeric()
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            })
                    )
                    ->visible(static::getTimeSettings()->enable_timesheets),
                TextColumn::make('total_hours_spent')
                    ->label(__('projects::filament/resources/task.table.columns.time-spent'))
                    ->sortable()
                    ->toggleable()
                    ->numeric()
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize(
                        Sum::make()
                            ->label(__('projects::filament/resources/task.table.columns.time-spent'))
                            ->numeric()
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            })
                    )
                    ->visible(static::getTimeSettings()->enable_timesheets),
                TextColumn::make('remaining_hours')
                    ->label(__('projects::filament/resources/task.table.columns.time-remaining'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize(
                        Sum::make()
                            ->label(__('projects::filament/resources/task.table.columns.time-remaining'))
                            ->numeric()
                            ->numeric()
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            })
                    )
                    ->visible(static::getTimeSettings()->enable_timesheets),
                ProgressBarEntry::make('progress')
                    ->label(__('projects::filament/resources/task.table.columns.progress'))
                    ->sortable()
                    ->toggleable()
                    ->color(fn (Task $record): string => $record->progress > 100 ? 'danger' : ($record->progress < 100 ? 'warning' : 'success'))
                    ->visible(static::getTimeSettings()->enable_timesheets),
                TextColumn::make('deadline')
                    ->label(__('projects::filament/resources/task.table.columns.deadline'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('tags.name')
                    ->label(__('projects::filament/resources/task.table.columns.tags'))
                    ->badge()
                    ->state(function (Task $record): array {
                        return $record->tags->map(fn ($tag) => [
                            'label' => $tag->name,
                            'color' => $tag->color ?? '#808080',
                        ])->toArray();
                    })
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state['label'])
                    ->color(fn ($state) => Color::generateV3Palette($state['color']))
                    ->toggleable(),
                TextColumn::make('stage.name')
                    ->label(__('projects::filament/resources/task.table.columns.stage'))
                    ->sortable()
                    ->toggleable(),
            ]))
            ->groups([
                Tables\Grouping\Group::make('state')
                    ->label(__('projects::filament/resources/task.table.groups.state'))
                    ->getTitleFromRecordUsing(fn (Task $record): string => TaskState::options()[$record->state]),
                Tables\Grouping\Group::make('project.name')
                    ->label(__('projects::filament/resources/task.table.groups.project')),
                Tables\Grouping\Group::make('deadline')
                    ->label(__('projects::filament/resources/task.table.groups.deadline'))
                    ->date(),
                Tables\Grouping\Group::make('stage.name')
                    ->label(__('projects::filament/resources/task.table.groups.stage')),
                Tables\Grouping\Group::make('milestone.name')
                    ->label(__('projects::filament/resources/task.table.groups.milestone')),
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('projects::filament/resources/task.table.groups.customer')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('projects::filament/resources/task.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc')
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect(static::mergeCustomTableQueryBuilderConstraints([
                        TextConstraint::make('title')
                            ->label(__('projects::filament/resources/task.table.filters.title')),
                        SelectConstraint::make('priority')
                            ->label(__('projects::filament/resources/task.table.filters.priority'))
                            ->options([
                                0 => __('projects::filament/resources/task.table.filters.low'),
                                1 => __('projects::filament/resources/task.table.filters.high'),
                            ])
                            ->icon('heroicon-o-star'),
                        SelectConstraint::make('state')
                            ->label(__('projects::filament/resources/task.table.filters.state'))
                            ->multiple()
                            ->options(TaskState::options())
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('tags')
                            ->label(__('projects::filament/resources/task.table.filters.tags'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-tag'),
                        static::getTimeSettings()->enable_timesheets
                            ? NumberConstraint::make('allocated_hours')
                                ->label(__('projects::filament/resources/task.table.filters.allocated-hours'))
                                ->icon('heroicon-o-clock')
                            : null,
                        static::getTimeSettings()->enable_timesheets
                            ? NumberConstraint::make('total_hours_spent')
                                ->label(__('projects::filament/resources/task.table.filters.total-hours-spent'))
                                ->icon('heroicon-o-clock')
                            : null,
                        static::getTimeSettings()->enable_timesheets
                            ? NumberConstraint::make('remaining_hours')
                                ->label(__('projects::filament/resources/task.table.filters.remaining-hours'))
                                ->icon('heroicon-o-clock')
                            : null,
                        static::getTimeSettings()->enable_timesheets
                            ? NumberConstraint::make('overtime')
                                ->label(__('projects::filament/resources/task.table.filters.overtime'))
                                ->icon('heroicon-o-clock')
                            : null,
                        static::getTimeSettings()->enable_timesheets
                            ? NumberConstraint::make('progress')
                                ->label(__('projects::filament/resources/task.table.filters.progress'))
                                ->icon('heroicon-o-bars-2')
                            : null,
                        DateConstraint::make('deadline')
                            ->label(__('projects::filament/resources/task.table.filters.deadline'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('created_at')
                            ->label(__('projects::filament/resources/task.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('projects::filament/resources/task.table.filters.updated-at')),
                        RelationshipConstraint::make('users')
                            ->label(__('projects::filament/resources/task.table.filters.assignees'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-users'),
                        RelationshipConstraint::make('partner')
                            ->label(__('projects::filament/resources/task.table.filters.customer'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('project')
                            ->label(__('projects::filament/resources/task.table.filters.project'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-folder'),
                        RelationshipConstraint::make('stage')
                            ->label(__('projects::filament/resources/task.table.filters.stage'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('milestone')
                            ->label(__('projects::filament/resources/task.table.filters.milestone'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-flag'),
                        RelationshipConstraint::make('company')
                            ->label(__('projects::filament/resources/task.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        RelationshipConstraint::make('creator')
                            ->label(__('projects::filament/resources/task.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                    ]))->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/task.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.actions.force-delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.bulk-actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/task.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.bulk-actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => static::can('delete', $record) || static::can('forceDelete', $record) || static::can('restore', $record),
            );
    }

    public static function infolist(Schema $schema): Schema
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
                                    ->icon(fn (TaskState $state): string => $state->getIcon())
                                    ->color(fn (TaskState $state): string => $state->getColor())
                                    ->formatStateUsing(fn (TaskState $state): string => $state->getLabel()),

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
                                        return $record->tags->map(fn ($tag) => [
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
                                            ->visible(static::getTaskSettings()->enable_milestones),

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
                                            ->visible(static::getTimeSettings()->enable_timesheets),

                                        TextEntry::make('total_hours_spent')
                                            ->label(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.time-spent'))
                                            ->icon('heroicon-o-clock')
                                            ->suffix(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.time-spent-suffix'))
                                            ->formatStateUsing(function ($state) {
                                                $hours = floor($state);
                                                $minutes = ($state - $hours) * 60;

                                                return $hours.':'.$minutes;
                                            })
                                            ->visible(static::getTimeSettings()->enable_timesheets),

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
                                            ->visible(static::getTimeSettings()->enable_timesheets),

                                        TextEntry::make('progress')
                                            ->label(__('projects::filament/resources/task.infolist.sections.time-tracking.entries.progress'))
                                            ->icon('heroicon-o-chart-bar')
                                            ->suffix('%')
                                            ->color(
                                                fn ($record): string => $record->progress > 100
                                                    ? 'danger'
                                                    : ($record->progress < 100 ? 'warning' : 'success')
                                            )
                                            ->visible(static::getTimeSettings()->enable_timesheets),
                                    ]),
                            ])
                            ->visible(static::getTimeSettings()->enable_timesheets),

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
                                    ->visible(static::getTimeSettings()->enable_timesheets),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    private static function getTimeSettings(): TimeSettings
    {
        return once(fn () => app(TimeSettings::class));
    }

    private static function getTaskSettings(): TaskSettings
    {
        return once(fn () => app(TaskSettings::class));
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewTask::class,
            EditTask::class,
            ManageTimesheets::class,
            ManageSubTasks::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Timesheets', [
                TimesheetsRelationManager::class,
            ])
                ->icon('heroicon-o-clock'),

            RelationGroup::make('Sub Tasks', [
                SubTasksRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-document-list'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListTasks::route('/'),
            'create'     => CreateTask::route('/create'),
            'edit'       => EditTask::route('/{record}/edit'),
            'view'       => ViewTask::route('/{record}'),
            'timesheets' => ManageTimesheets::route('/{record}/timesheets'),
            'sub-tasks'  => ManageSubTasks::route('/{record}/sub-tasks'),
        ];
    }
}
