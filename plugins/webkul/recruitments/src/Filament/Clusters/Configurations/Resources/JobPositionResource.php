<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Filament\Resources\DepartmentResource;
use Webkul\Employee\Models\Department;
use Webkul\Partner\Filament\Resources\IndustryResource;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositionResource\Pages;
use Webkul\Recruitment\Models\JobPosition;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\UserResource;

class JobPositionResource extends Resource
{
    protected static ?string $model = JobPosition::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/job-position.navigation.group');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('recruitments::filament/clusters/configurations/resources/job-position.global-search.name')            => $record->name ?? '—',
            __('recruitments::filament/clusters/configurations/resources/job-position.global-search.department')      => $record->department?->name ?? '—',
            __('recruitments::filament/clusters/configurations/resources/job-position.global-search.employment-type') => $record->employmentType?->name ?? '—',
            __('recruitments::filament/clusters/configurations/resources/job-position.global-search.company')         => $record->company?->name ?? '—',
            __('recruitments::filament/clusters/configurations/resources/job-position.global-search.created-by')      => $record->createdBy?->name ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.title'))
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-position-title'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-position-title-tooltip')),
                                        Forms\Components\Select::make('department_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department'))
                                            ->relationship(name: 'department', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                                $department = Department::find($state);

                                                if (
                                                    ! $get('manager_id')
                                                    && $department?->manager_id
                                                ) {
                                                    $set('manager_id', $department->manager_id);
                                                }

                                                if (
                                                    ! $get('company_id')
                                                    && $department?->company_id
                                                ) {
                                                    $set('company_id', $department->company_id);
                                                }
                                            })
                                            ->createOptionForm(fn (Form $form) => DepartmentResource::form($form))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department-modal-title'));
                                            }),
                                        Forms\Components\Select::make('manager_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.manager'))
                                            ->relationship(name: 'manager', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->reactive()
                                            ->live(),
                                        Forms\Components\Select::make('company_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company'))
                                            ->relationship(name: 'company', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->createOptionForm(fn (Form $form) => CompanyResource::form($form))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalIcon('heroicon-o-building-office')
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company-modal-title'));
                                            }),
                                        Forms\Components\Select::make('recruiter_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.recruiter'))
                                            ->relationship(name: 'recruiter', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->createOptionForm(fn (Form $form) => CompanyResource::form($form))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalIcon('heroicon-o-building-office')
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company-modal-title'));
                                            }),
                                        Forms\Components\Select::make('recruitments_job_position_interviewers')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.interviewers'))
                                            ->relationship(name: 'interviewers', titleAttribute: 'name')
                                            ->searchable()
                                            ->multiple()
                                            ->preload()
                                            ->live()
                                            ->createOptionForm(fn (Form $form) => UserResource::form($form)),
                                        Forms\Components\Select::make('address_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-location'))
                                            ->relationship(name: 'address', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Form $form) => DepartmentResource::form($form))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department-modal-title'))
                                                    ->modalSubmitActionLabel(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department-modal-title'))
                                                    ->modalWidth('2xl');
                                            }),
                                        Forms\Components\Select::make('industry_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.industry'))
                                            ->relationship('industry', 'name')
                                            ->searchable()
                                            ->createOptionForm(fn ($form) => IndustryResource::form($form)->columns(2))
                                            ->preload(),
                                    ])->columns(2),
                                Forms\Components\Section::make()
                                    ->hiddenLabel()
                                    ->schema([
                                        Forms\Components\RichEditor::make('description')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.job-description.fields.job-description'))
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('requirements')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.job-description.fields.job-requirements'))
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\DatePicker::make('date_from')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.date-from'))
                                            ->native(false),
                                        Forms\Components\DatePicker::make('date_to')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.date-to'))
                                            ->native(false),
                                    ])->columns(2),
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('no_of_recruitment')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.recruitment-target'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->default(0),
                                        Forms\Components\TextInput::make('no_of_employee')
                                            ->disabled()
                                            ->dehydrated(false),
                                        Forms\Components\TextInput::make('expected_employees')
                                            ->disabled()
                                            ->dehydrated(false),
                                        Forms\Components\TextInput::make('no_of_hired_employee')
                                            ->disabled(),
                                        Forms\Components\Select::make('job_position_skills')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.expected-skills'))
                                            ->relationship('skills', 'name')
                                            ->searchable()
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('employment_type_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.employment-type'))
                                            ->relationship('employmentType', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.status'))
                                            ->inline(false),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.job-position'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.department'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.company'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_employees')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.expected-employees'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('no_of_employee')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.current-employees'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.status'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.department')),
                Tables\Filters\SelectFilter::make('employmentType')
                    ->relationship('employmentType', 'name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.employment-type')),
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.company')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.status')),
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.job-position'))
                            ->icon('heroicon-o-building-office-2'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.company'))
                            ->icon('heroicon-o-building-office')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('department')
                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.department'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('employmentType')
                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.employment-type'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('creator')
                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.updated-at')),
                    ]),
            ])
            ->filtersFormColumns(2)
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.groups.job-position'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('department.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.groups.department'))
                    ->collapsible(),
                Tables\Grouping\Group::make('employmentType.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.groups.employment-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.actions.delete.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.actions.delete.notification.body'))
                    ),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.actions.restore.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.actions.restore.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.delete.notification.body'))
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.force-delete.notification.body'))
                        ),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.restore.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.empty-state-actions.create.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.empty-state-actions.create.notification.body'))
                    ),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 3])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->icon('heroicon-o-briefcase')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.job-position-title')),
                                        Infolists\Components\TextEntry::make('department.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.department')),
                                        Infolists\Components\TextEntry::make('manager.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.manager')),
                                        Infolists\Components\TextEntry::make('company.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.company')),
                                        Infolists\Components\TextEntry::make('recruiter.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.recruiter')),
                                        Infolists\Components\TextEntry::make('interviewers.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->listWithLineBreaks()
                                            ->badge()
                                            ->listWithLineBreaks()
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.interviewers')),
                                        Infolists\Components\TextEntry::make('address.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office-2')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.job-location')),
                                        Infolists\Components\TextEntry::make('industry.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office-2')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.industry')),
                                    ])->columns(2),
                                Infolists\Components\Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('description')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.entries.job-description'))
                                            ->placeholder('—')
                                            ->html()
                                            ->columnSpanFull(),
                                        Infolists\Components\TextEntry::make('requirements')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.entries.job-requirements'))
                                            ->placeholder('—')
                                            ->html()
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Infolists\Components\Group::make([
                            Infolists\Components\Section::make()
                                ->schema([
                                    Infolists\Components\TextEntry::make('date_from')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.date-from'))
                                        ->placeholder('—')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d') : null)
                                        ->icon('heroicon-o-calendar'),
                                    Infolists\Components\TextEntry::make('date_to')
                                        ->icon('heroicon-o-calendar')
                                        ->placeholder('—')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d') : null)
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.date-to')),
                                ]),
                            Infolists\Components\Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.title'))
                                ->schema([
                                    Infolists\Components\TextEntry::make('expected_employees')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.expected-employees'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-user-group')
                                        ->numeric(),
                                    Infolists\Components\TextEntry::make('no_of_employee')
                                        ->icon('heroicon-o-user-group')
                                        ->placeholder('—')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.current-employees'))
                                        ->numeric(),
                                    Infolists\Components\TextEntry::make('no_of_recruitment')
                                        ->icon('heroicon-o-user-group')
                                        ->placeholder('—')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.recruitment-target'))
                                        ->numeric(),
                                    Infolists\Components\TextEntry::make('employmentType.name')
                                        ->placeholder('—')
                                        ->icon('heroicon-o-briefcase')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.employment-type')),
                                    Infolists\Components\IconEntry::make('is_active')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.position-status.entries.status')),
                                    Infolists\Components\TextEntry::make('skills.name')
                                        ->listWithLineBreaks()
                                        ->badge()
                                        ->listWithLineBreaks()
                                        ->label(__('Expected Skills')),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJobPositions::route('/'),
            'create' => Pages\CreateJobPosition::route('/create'),
            'edit'   => Pages\EditJobPosition::route('/{record}/edit'),
            'view'   => Pages\ViewJobPosition::route('/{record}'),
        ];
    }
}
