<?php

namespace Webkul\Project\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Project\Enums\ProjectVisibility;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\CreateProject;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\EditProject;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ListProjects;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageMilestones;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageTasks;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ViewProject;
use Webkul\Project\Filament\Resources\ProjectResource\RelationManagers\MilestonesRelationManager;
use Webkul\Project\Filament\Resources\ProjectResource\RelationManagers\TaskStagesRelationManager;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\ProjectStage;
use Webkul\Project\Settings\TaskSettings;
use Webkul\Project\Settings\TimeSettings;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\UserResource;

class ProjectResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Project::class;

    protected static ?string $slug = 'project/projects';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/project.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('projects::filament/resources/project.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'user.name', 'partner.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('projects::filament/resources/project.global-search.project-manager') => $record->user?->name ?? '—',
            __('projects::filament/resources/project.global-search.customer')        => $record->partner?->name ?? '—',
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
                            ->visible(static::getTaskSettings()->enable_project_stages)
                            ->options(fn () => ProjectStage::orderBy('sort')->get()->mapWithKeys(fn ($stage) => [$stage->id => $stage->name]))
                            ->default(ProjectStage::first()?->id),
                        Section::make(__('projects::filament/resources/project.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('projects::filament/resources/project.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('projects::filament/resources/project.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                RichEditor::make('description')
                                    ->label(__('projects::filament/resources/project.form.sections.general.fields.description')),
                            ]),

                        Section::make(__('projects::filament/resources/project.form.sections.additional.title'))
                            ->schema(static::mergeCustomFormFields([
                                Select::make('user_id')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.project-manager'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => UserResource::form($schema)),
                                Select::make('partner_id')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.customer'))
                                    ->relationship('partner', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => PartnerResource::form($schema))
                                    ->editOptionForm(fn (Schema $schema) => PartnerResource::form($schema)),
                                DatePicker::make('start_date')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.start-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->requiredWith('end_date')
                                    ->beforeOrEqual('start_date'),
                                DatePicker::make('end_date')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.end-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->requiredWith('start_date')
                                    ->afterOrEqual('start_date'),
                                TextInput::make('allocated_hours')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.allocated-hours'))
                                    ->suffixIcon('heroicon-o-clock')
                                    ->minValue(0)
                                    ->numeric()
                                    ->helperText(__('projects::filament/resources/project.form.sections.additional.fields.allocated-hours-helper-text'))
                                    ->visible(static::getTimeSettings()->enable_timesheets)
                                    ->rules(['nullable', 'numeric', 'min:0']),
                                Select::make('tags')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.tags'))
                                    ->relationship(name: 'tags', titleAttribute: 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => TagResource::form($schema)),
                                Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.company'))
                                    ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema)),
                            ]))
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('projects::filament/resources/project.form.sections.settings.title'))
                            ->schema([
                                Radio::make('visibility')
                                    ->label(__('projects::filament/resources/project.form.sections.settings.fields.visibility'))
                                    ->default('internal')
                                    ->options(ProjectVisibility::options())
                                    ->descriptions([
                                        'private'  => __('projects::filament/resources/project.form.sections.settings.fields.private-description'),
                                        'internal' => __('projects::filament/resources/project.form.sections.settings.fields.internal-description'),
                                        'public'   => __('projects::filament/resources/project.form.sections.settings.fields.public-description'),
                                    ])
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('projects::filament/resources/project.form.sections.settings.fields.visibility-hint-tooltip')),

                                Fieldset::make(__('projects::filament/resources/project.form.sections.settings.fields.time-management'))
                                    ->schema([
                                        Toggle::make('allow_timesheets')
                                            ->label(__('projects::filament/resources/project.form.sections.settings.fields.allow-timesheets'))
                                            ->helperText(__('projects::filament/resources/project.form.sections.settings.fields.allow-timesheets-helper-text'))
                                            ->visible(static::getTimeSettings()->enable_timesheets),
                                    ])
                                    ->columns(1)
                                    ->visible(static::getTimeSettings()->enable_timesheets)
                                    ->default(static::getTimeSettings()->enable_timesheets),

                                Fieldset::make(__('projects::filament/resources/project.form.sections.settings.fields.task-management'))
                                    ->schema([
                                        Toggle::make('allow_milestones')
                                            ->label(__('projects::filament/resources/project.form.sections.settings.fields.allow-milestones'))
                                            ->helperText(__('projects::filament/resources/project.form.sections.settings.fields.allow-milestones-helper-text'))
                                            ->visible(static::getTaskSettings()->enable_milestones)
                                            ->default(static::getTaskSettings()->enable_milestones),
                                    ])
                                    ->columns(1)
                                    ->visible(static::getTaskSettings()->enable_milestones),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::mergeCustomTableColumns([
                Stack::make([
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->label(__('projects::filament/resources/project.table.columns.name'))
                            ->searchable()
                            ->sortable(),
                    ]),
                    Stack::make([
                        TextColumn::make('partner.name')
                            ->label(__('projects::filament/resources/project.table.columns.customer'))
                            ->icon('heroicon-o-phone')
                            ->tooltip(__('projects::filament/resources/project.table.columns.customer'))
                            ->sortable(),
                    ])
                        ->visible(fn (Project $record) => filled($record->partner)),
                    Stack::make([
                        TextColumn::make('start_date')
                            ->label(__('projects::filament/resources/project.table.columns.start-date'))
                            ->sortable()
                            ->extraAttributes(['class' => 'hidden']),
                        TextColumn::make('end_date')
                            ->label(__('projects::filament/resources/project.table.columns.end-date'))
                            ->sortable()
                            ->extraAttributes(['class' => 'hidden']),
                        TextColumn::make('planned_date')
                            ->icon('heroicon-o-calendar')
                            ->tooltip(__('projects::filament/resources/project.table.columns.planned-date'))
                            ->state(fn (Project $record): string => $record->start_date->format('d M Y').' - '.$record->end_date->format('d M Y')),
                    ])
                        ->visible(fn (Project $record) => filled($record->start_date) && filled($record->end_date)),
                    Stack::make([
                        TextColumn::make('remaining_hours')
                            ->icon('heroicon-o-clock')
                            ->badge()
                            ->color('success')
                            ->color(fn (Project $record): string => $record->remaining_hours < 0 ? 'danger' : 'success')
                            ->state(fn (Project $record): string => $record->remaining_hours.' Hours')
                            ->tooltip(__('projects::filament/resources/project.table.columns.remaining-hours')),
                    ])
                        ->visible(fn (Project $record) => static::getTimeSettings()->enable_timesheets && $record->allow_milestones && $record->remaining_hours),
                    Stack::make([
                        TextColumn::make('user.name')
                            ->label(__('projects::filament/resources/project.table.columns.project-manager'))
                            ->icon('heroicon-o-user')
                            ->label(__('projects::filament/resources/project.table.columns.project-manager'))
                            ->sortable(),
                    ])
                        ->visible(fn (Project $record) => filled($record->user)),
                    Stack::make([
                        TextColumn::make('tags.name')
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
                            ->weight(FontWeight::Bold),
                    ])
                        ->visible(fn (Project $record): bool => (bool) $record->tags?->count()),
                ])
                    ->space(3),
            ]))
            ->groups([
                Tables\Grouping\Group::make('stage.name')
                    ->label(__('projects::filament/resources/project.table.groups.stage')),
                Tables\Grouping\Group::make('user.name')
                    ->label(__('projects::filament/resources/project.table.groups.project-manager')),
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('projects::filament/resources/project.table.groups.customer')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('projects::filament/resources/project.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc')
            ->filters([
                QueryBuilder::make()
                    ->constraints(static::mergeCustomTableQueryBuilderConstraints([
                        TextConstraint::make('name')
                            ->label(__('projects::filament/resources/project.table.filters.name')),
                        SelectConstraint::make('visibility')
                            ->label(__('projects::filament/resources/project.table.filters.visibility'))
                            ->multiple()
                            ->options(ProjectVisibility::options())
                            ->icon('heroicon-o-eye'),
                        DateConstraint::make('start_date')
                            ->label(__('projects::filament/resources/project.table.filters.start-date')),
                        DateConstraint::make('end_date')
                            ->label(__('projects::filament/resources/project.table.filters.end-date')),
                        BooleanConstraint::make('allow_timesheets')
                            ->label(__('projects::filament/resources/project.table.filters.allow-timesheets'))
                            ->icon('heroicon-o-clock'),
                        BooleanConstraint::make('allow_milestones')
                            ->label(__('projects::filament/resources/project.table.filters.allow-milestones'))
                            ->icon('heroicon-o-flag'),
                        NumberConstraint::make('allocated_hours')
                            ->label(__('projects::filament/resources/project.table.filters.allocated-hours'))
                            ->icon('heroicon-o-clock'),
                        DateConstraint::make('created_at')
                            ->label(__('projects::filament/resources/project.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('projects::filament/resources/project.table.filters.updated-at')),
                        RelationshipConstraint::make('stage')
                            ->label(__('projects::filament/resources/project.table.filters.stage'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('partner')
                            ->label(__('projects::filament/resources/project.table.filters.customer'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('user')
                            ->label(__('projects::filament/resources/project.table.filters.project-manager'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('company')
                            ->label(__('projects::filament/resources/project.table.filters.company'))
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
                            ->label(__('projects::filament/resources/project.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('tags')
                            ->label(__('projects::filament/resources/project.table.filters.tags'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-tag'),
                    ])),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                Action::make('is_favorite_by_user')
                    ->hiddenLabel()
                    ->icon(fn (Project $record): string => $record->is_favorite_by_user ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Project $record): string => $record->is_favorite_by_user ? 'warning' : 'gray')
                    ->size('xl')
                    ->action(function (Project $record): void {
                        $record->favoriteUsers()->toggle([Auth::id()]);

                        $record->load(['favoriteUsers' => function ($query) {
                            $query->where('user_id', Auth::id());
                        }]);
                    }),
                Action::make('tasks')
                    ->label(fn (Project $record): string => __('projects::filament/resources/project.table.actions.tasks', ['count' => $record->tasks->whereNull('parent_id')->count()]))
                    ->icon('heroicon-m-clipboard-document-list')
                    ->color('gray')
                    ->url('https:example.com/tasks/{record}')
                    ->hidden(fn ($record) => $record->trashed())
                    ->url(fn (Project $record): string => ManageTasks::getUrl(['record' => $record])),
                Action::make('milestones')
                    ->label(fn (Project $record): string => $record->milestones->where('is_completed', true)->count().'/'.$record->milestones->count())
                    ->icon('heroicon-m-flag')
                    ->color('gray')
                    ->tooltip(fn (Project $record): string => __('projects::filament/resources/project.table.actions.milestones', ['completed' => $record->milestones->where('is_completed', true)->count(), 'all' => $record->milestones->count()]))
                    ->url('https:example.com/tasks/{record}')
                    ->hidden(fn (Project $record) => $record->trashed())
                    ->visible(fn (Project $record) => static::getTaskSettings()->enable_milestones && $record->allow_milestones)
                    ->url(fn (Project $record): string => ManageMilestones::getUrl(['record' => $record])),

                ActionGroup::make([
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project.table.actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/project.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project.table.actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/project.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->action(function (Model $record) {
                            try {
                                $record->forceDelete();
                                Notification::make()
                                    ->success()
                                    ->title(__('projects::filament/resources/project.table.actions.force-delete.notification.success.title'))
                                    ->body(__('projects::filament/resources/project.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException $th) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('projects::filament/resources/project.table.actions.force-delete.notification.error.title'))
                                    ->body(__('projects::filament/resources/project.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ])
                    ->link()
                    ->hiddenLabel(),
            ])
            ->recordUrl(fn (Project $record): string => static::getUrl('view', ['record' => $record]))
            ->contentGrid([
                'sm'  => 1,
                'md'  => 2,
                'xl'  => 3,
                '2xl' => 4,
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->with([
                    'milestones',
                    'favoriteUsers' => function ($query) {
                        $query->where('user_id', Auth::id());
                    },
                ]);
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
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
                            ->schema(static::mergeCustomInfolistEntries([
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
                                            ->visible(static::getTimeSettings()->enable_timesheets),

                                        TextEntry::make('remaining_hours')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.remaining-hours'))
                                            ->icon('heroicon-o-clock')
                                            ->suffix(__('projects::filament/resources/project.infolist.sections.additional.entries.remaining-hours-suffix'))
                                            ->color(fn (Project $record): string => $record->remaining_hours < 0 ? 'danger' : 'success')
                                            ->visible(static::getTimeSettings()->enable_timesheets),

                                        TextEntry::make('stage.name')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.current-stage'))
                                            ->icon('heroicon-o-flag')
                                            ->badge()
                                            ->visible(static::getTaskSettings()->enable_project_stages),

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
                            ])),

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
                                            ->visible(fn (Project $record) => static::getTaskSettings()->enable_milestones && $record->allow_milestones),
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
                                    ->visible(static::getTimeSettings()->enable_timesheets),

                                IconEntry::make('allow_milestones')
                                    ->label(__('projects::filament/resources/project.infolist.sections.settings.entries.milestones-enabled'))
                                    ->boolean()
                                    ->visible(static::getTaskSettings()->enable_milestones),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    static public function getTaskSettings(): TaskSettings
    {
        return once(fn () => app(TaskSettings::class));
    }

    static public function getTimeSettings(): TimeSettings
    {
        return once(fn () => app(TimeSettings::class));
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProject::class,
            EditProject::class,
            ManageTasks::class,
            ManageMilestones::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Task Stages', [
                TaskStagesRelationManager::class,
            ])
                ->icon('heroicon-o-squares-2x2'),

            RelationGroup::make('Milestones', [
                MilestonesRelationManager::class,
            ])
                ->icon('heroicon-o-flag'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListProjects::route('/'),
            'create'     => CreateProject::route('/create'),
            'edit'       => EditProject::route('/{record}/edit'),
            'view'       => ViewProject::route('/{record}'),
            'milestones' => ManageMilestones::route('/{record}/milestones'),
            'tasks'      => ManageTasks::route('/{record}/tasks'),
        ];
    }
}
