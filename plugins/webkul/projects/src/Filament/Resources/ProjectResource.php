<?php

namespace Webkul\Project\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Project\Enums\ProjectVisibility;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Project\Filament\Resources\ProjectResource\Pages;
use Webkul\Project\Filament\Resources\ProjectResource\RelationManagers;
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

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        ProgressStepper::make('stage_id')
                            ->hiddenLabel()
                            ->inline()
                            ->required()
                            ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_project_stages)
                            ->options(fn () => ProjectStage::orderBy('sort')->get()->mapWithKeys(fn ($stage) => [$stage->id => $stage->name]))
                            ->default(ProjectStage::first()?->id),
                        Forms\Components\Section::make(__('projects::filament/resources/project.form.sections.general.title'))
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('projects::filament/resources/project.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('projects::filament/resources/project.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                Forms\Components\RichEditor::make('description')
                                    ->label(__('projects::filament/resources/project.form.sections.general.fields.description')),
                            ]),

                        Forms\Components\Section::make(__('projects::filament/resources/project.form.sections.additional.title'))
                            ->schema(static::mergeCustomFormFields([
                                Forms\Components\Select::make('user_id')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.project-manager'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Form $form) => UserResource::form($form)),
                                Forms\Components\Select::make('partner_id')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.customer'))
                                    ->relationship('partner', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Form $form) => PartnerResource::form($form))
                                    ->editOptionForm(fn (Form $form) => PartnerResource::form($form)),
                                Forms\Components\DatePicker::make('start_date')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.start-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->requiredWith('end_date')
                                    ->beforeOrEqual('start_date'),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.end-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->requiredWith('start_date')
                                    ->afterOrEqual('start_date'),
                                Forms\Components\TextInput::make('allocated_hours')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.allocated-hours'))
                                    ->suffixIcon('heroicon-o-clock')
                                    ->minValue(0)
                                    ->helperText(__('projects::filament/resources/project.form.sections.additional.fields.allocated-hours-helper-text'))
                                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                                Forms\Components\Select::make('tags')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.tags'))
                                    ->relationship(name: 'tags', titleAttribute: 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Form $form) => TagResource::form($form)),
                                Forms\Components\Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.company'))
                                    ->createOptionForm(fn (Form $form) => CompanyResource::form($form)),
                            ]))
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('projects::filament/resources/project.form.sections.settings.title'))
                            ->schema([
                                Forms\Components\Radio::make('visibility')
                                    ->label(__('projects::filament/resources/project.form.sections.settings.fields.visibility'))
                                    ->default('internal')
                                    ->options(ProjectVisibility::options())
                                    ->descriptions([
                                        'private'  => __('projects::filament/resources/project.form.sections.settings.fields.private-description'),
                                        'internal' => __('projects::filament/resources/project.form.sections.settings.fields.internal-description'),
                                        'public'   => __('projects::filament/resources/project.form.sections.settings.fields.public-description'),
                                    ])
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('projects::filament/resources/project.form.sections.settings.fields.visibility-hint-tooltip')),

                                Forms\Components\Fieldset::make(__('projects::filament/resources/project.form.sections.settings.fields.time-management'))
                                    ->schema([
                                        Forms\Components\Toggle::make('allow_timesheets')
                                            ->label(__('projects::filament/resources/project.form.sections.settings.fields.allow-timesheets'))
                                            ->helperText(__('projects::filament/resources/project.form.sections.settings.fields.allow-timesheets-helper-text'))
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                                    ])
                                    ->columns(1)
                                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets)
                                    ->default(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                                Forms\Components\Fieldset::make(__('projects::filament/resources/project.form.sections.settings.fields.task-management'))
                                    ->schema([
                                        Forms\Components\Toggle::make('allow_milestones')
                                            ->label(__('projects::filament/resources/project.form.sections.settings.fields.allow-milestones'))
                                            ->helperText(__('projects::filament/resources/project.form.sections.settings.fields.allow-milestones-helper-text'))
                                            ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_milestones)
                                            ->default(fn (TaskSettings $taskSettings) => $taskSettings->enable_milestones),
                                    ])
                                    ->columns(1)
                                    ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_milestones),
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
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->label(__('projects::filament/resources/project.table.columns.name'))
                            ->searchable()
                            ->sortable(),
                    ]),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('partner.name')
                            ->label(__('projects::filament/resources/project.table.columns.customer'))
                            ->icon('heroicon-o-phone')
                            ->tooltip(__('projects::filament/resources/project.table.columns.customer'))
                            ->sortable(),
                    ])
                        ->visible(fn (Project $record) => filled($record->partner)),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('start_date')
                            ->label(__('projects::filament/resources/project.table.columns.start-date'))
                            ->sortable()
                            ->extraAttributes(['class' => 'hidden']),
                        Tables\Columns\TextColumn::make('end_date')
                            ->label(__('projects::filament/resources/project.table.columns.end-date'))
                            ->sortable()
                            ->extraAttributes(['class' => 'hidden']),
                        Tables\Columns\TextColumn::make('planned_date')
                            ->icon('heroicon-o-calendar')
                            ->tooltip(__('projects::filament/resources/project.table.columns.planned-date'))
                            ->state(fn (Project $record): string => $record->start_date->format('d M Y').' - '.$record->end_date->format('d M Y')),
                    ])
                        ->visible(fn (Project $record) => filled($record->start_date) && filled($record->end_date)),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('remaining_hours')
                            ->icon('heroicon-o-clock')
                            ->badge()
                            ->color('success')
                            ->color(fn (Project $record): string => $record->remaining_hours < 0 ? 'danger' : 'success')
                            ->state(fn (Project $record): string => $record->remaining_hours.' Hours')
                            ->tooltip(__('projects::filament/resources/project.table.columns.remaining-hours')),
                    ])
                        ->visible(fn (TimeSettings $timeSettings, Project $record) => $timeSettings->enable_timesheets && $record->allow_milestones && $record->remaining_hours),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('user.name')
                            ->label(__('projects::filament/resources/project.table.columns.project-manager'))
                            ->icon('heroicon-o-user')
                            ->label(__('projects::filament/resources/project.table.columns.project-manager'))
                            ->sortable(),
                    ])
                        ->visible(fn (Project $record) => filled($record->user)),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('tags.name')
                            ->badge()
                            ->state(function (Project $record): array {
                                return $record->tags()->get()->map(fn ($tag) => [
                                    'label' => $tag->name,
                                    'color' => $tag->color ?? '#808080',
                                ])->toArray();
                            })
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state['label'])
                            ->color(fn ($state) => Color::hex($state['color']))
                            ->weight(FontWeight::Bold),
                    ])
                        ->visible(fn (Project $record): bool => (bool) $record->tags()->get()?->count()),
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
                Tables\Filters\QueryBuilder::make()
                    ->constraints(static::mergeCustomTableQueryBuilderConstraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('projects::filament/resources/project.table.filters.name')),
                        Tables\Filters\QueryBuilder\Constraints\SelectConstraint::make('visibility')
                            ->label(__('projects::filament/resources/project.table.filters.visibility'))
                            ->multiple()
                            ->options(ProjectVisibility::options())
                            ->icon('heroicon-o-eye'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('start_date')
                            ->label(__('projects::filament/resources/project.table.filters.start-date')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('end_date')
                            ->label(__('projects::filament/resources/project.table.filters.end-date')),
                        Tables\Filters\QueryBuilder\Constraints\BooleanConstraint::make('allow_timesheets')
                            ->label(__('projects::filament/resources/project.table.filters.allow-timesheets'))
                            ->icon('heroicon-o-clock'),
                        Tables\Filters\QueryBuilder\Constraints\BooleanConstraint::make('allow_milestones')
                            ->label(__('projects::filament/resources/project.table.filters.allow-milestones'))
                            ->icon('heroicon-o-flag'),
                        Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('allocated_hours')
                            ->label(__('projects::filament/resources/project.table.filters.allocated-hours'))
                            ->icon('heroicon-o-clock'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('projects::filament/resources/project.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('projects::filament/resources/project.table.filters.updated-at')),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('stage')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('partner')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('user')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('creator')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('tags')
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
            ], layout: \Filament\Tables\Enums\FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\Action::make('is_favorite_by_user')
                    ->hiddenLabel()
                    ->icon(fn (Project $record): string => $record->is_favorite_by_user ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Project $record): string => $record->is_favorite_by_user ? 'warning' : 'gray')
                    ->size('xl')
                    ->action(function (Project $record): void {
                        $record->favoriteUsers()->toggle([Auth::id()]);
                    }),
                Tables\Actions\Action::make('tasks')
                    ->label(fn (Project $record): string => __('projects::filament/resources/project.table.actions.tasks', ['count' => $record->tasks->whereNull('parent_id')->count()]))
                    ->icon('heroicon-m-clipboard-document-list')
                    ->color('gray')
                    ->url('https:example.com/tasks/{record}')
                    ->hidden(fn ($record) => $record->trashed())
                    ->url(fn (Project $record): string => Pages\ManageTasks::getUrl(['record' => $record])),
                Tables\Actions\Action::make('milestones')
                    ->label(fn (Project $record): string => $record->milestones->where('is_completed', true)->count().'/'.$record->milestones->count())
                    ->icon('heroicon-m-flag')
                    ->color('gray')
                    ->tooltip(fn (Project $record): string => __('projects::filament/resources/project.table.actions.milestones', ['completed' => $record->milestones->where('is_completed', true)->count(), 'all' => $record->milestones->count()]))
                    ->url('https:example.com/tasks/{record}')
                    ->hidden(fn (Project $record) => $record->trashed())
                    ->visible(fn (TaskSettings $taskSettings, Project $record) => $taskSettings->enable_milestones && $record->allow_milestones)
                    ->url(fn (Project $record): string => Pages\ManageMilestones::getUrl(['record' => $record])),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project.table.actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/project.table.actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project.table.actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/project.table.actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project.table.actions.force-delete.notification.title'))
                                ->body(__('projects::filament/resources/project.table.actions.force-delete.notification.body')),
                        ),
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
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('projects::filament/resources/project.infolist.sections.general.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('projects::filament/resources/project.infolist.sections.general.entries.name'))
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                                Infolists\Components\TextEntry::make('description')
                                    ->label(__('projects::filament/resources/project.infolist.sections.general.entries.description'))
                                    ->markdown(),
                            ]),

                        Infolists\Components\Section::make(__('projects::filament/resources/project.infolist.sections.additional.title'))
                            ->schema(static::mergeCustomInfolistEntries([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('user.name')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.project-manager'))
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—'),

                                        Infolists\Components\TextEntry::make('partner.name')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.customer'))
                                            ->icon('heroicon-o-phone')
                                            ->placeholder('—'),

                                        Infolists\Components\TextEntry::make('planned_date')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.project-timeline'))
                                            ->icon('heroicon-o-calendar')
                                            ->state(function (Project $record): ?string {
                                                if (! $record->start_date || ! $record->end_date) {
                                                    return '—';
                                                }

                                                return $record->start_date->format('d M Y').' - '.$record->end_date->format('d M Y');
                                            }),

                                        Infolists\Components\TextEntry::make('allocated_hours')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.allocated-hours'))
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—')
                                            ->suffix(__('projects::filament/resources/project.infolist.sections.additional.entries.allocated-hours-suffix'))
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                                        Infolists\Components\TextEntry::make('remaining_hours')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.remaining-hours'))
                                            ->icon('heroicon-o-clock')
                                            ->suffix(__('projects::filament/resources/project.infolist.sections.additional.entries.remaining-hours-suffix'))
                                            ->color(fn (Project $record): string => $record->remaining_hours < 0 ? 'danger' : 'success')
                                            ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                                        Infolists\Components\TextEntry::make('stage.name')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.current-stage'))
                                            ->icon('heroicon-o-flag')
                                            ->badge()
                                            ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_project_stages),

                                        Infolists\Components\TextEntry::make('tags.name')
                                            ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.tags'))
                                            ->badge()
                                            ->state(function (Project $record): array {
                                                return $record->tags()->get()->map(fn ($tag) => [
                                                    'label' => $tag->name,
                                                    'color' => $tag->color ?? '#808080',
                                                ])->toArray();
                                            })
                                            ->badge()
                                            ->formatStateUsing(fn ($state) => $state['label'])
                                            ->color(fn ($state) => Color::hex($state['color']))
                                            ->listWithLineBreaks()
                                            ->separator(', ')
                                            ->weight(\Filament\Support\Enums\FontWeight::Bold),
                                    ]),
                            ])),

                        Infolists\Components\Section::make(__('projects::filament/resources/project.infolist.sections.statistics.title'))
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('tasks_count')
                                            ->label(__('projects::filament/resources/project.infolist.sections.statistics.entries.total-tasks'))
                                            ->state(fn (Project $record): int => $record->tasks()->count())
                                            ->icon('heroicon-m-clipboard-document-list')
                                            ->iconColor('primary')
                                            ->color('primary')
                                            ->url(fn (Project $record): string => Pages\ManageTasks::getUrl(['record' => $record])),

                                        Infolists\Components\TextEntry::make('milestones_completion')
                                            ->label(__('projects::filament/resources/project.infolist.sections.statistics.entries.milestones-progress'))
                                            ->state(function (Project $record): string {
                                                $completed = $record->milestones()->where('is_completed', true)->count();
                                                $total = $record->milestones()->count();

                                                return "{$completed}/{$total}";
                                            })
                                            ->icon('heroicon-m-flag')
                                            ->iconColor('primary')
                                            ->color('primary')
                                            ->url(fn (Project $record): string => Pages\ManageMilestones::getUrl(['record' => $record]))
                                            ->visible(fn (TaskSettings $taskSettings, Project $record) => $taskSettings->enable_milestones && $record->allow_milestones),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('projects::filament/resources/project.infolist.sections.record-information.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('projects::filament/resources/project.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('projects::filament/resources/project.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('projects::filament/resources/project.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),

                        Infolists\Components\Section::make(__('projects::filament/resources/project.infolist.sections.settings.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('visibility')
                                    ->label(__('projects::filament/resources/project.infolist.sections.settings.entries.visibility'))
                                    ->badge()
                                    ->icon(fn (string $state): string => ProjectVisibility::icons()[$state])
                                    ->color(fn (string $state): string => ProjectVisibility::colors()[$state])
                                    ->formatStateUsing(fn (string $state): string => ProjectVisibility::options()[$state]),

                                Infolists\Components\IconEntry::make('allow_timesheets')
                                    ->label(__('projects::filament/resources/project.infolist.sections.settings.entries.timesheets-enabled'))
                                    ->boolean()
                                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),

                                Infolists\Components\IconEntry::make('allow_milestones')
                                    ->label(__('projects::filament/resources/project.infolist.sections.settings.entries.milestones-enabled'))
                                    ->boolean()
                                    ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_milestones),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewProject::class,
            Pages\EditProject::class,
            Pages\ManageTasks::class,
            Pages\ManageMilestones::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Task Stages', [
                RelationManagers\TaskStagesRelationManager::class,
            ])
                ->icon('heroicon-o-squares-2x2'),

            RelationGroup::make('Milestones', [
                RelationManagers\MilestonesRelationManager::class,
            ])
                ->icon('heroicon-o-flag'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListProjects::route('/'),
            'create'     => Pages\CreateProject::route('/create'),
            'edit'       => Pages\EditProject::route('/{record}/edit'),
            'view'       => Pages\ViewProject::route('/{record}'),
            'milestones' => Pages\ManageMilestones::route('/{record}/milestones'),
            'tasks'      => Pages\ManageTasks::route('/{record}/tasks'),
        ];
    }
}
