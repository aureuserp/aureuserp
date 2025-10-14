<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Filament\Resources\DepartmentResource;
use Webkul\Employee\Models\Department;
use Webkul\Partner\Filament\Resources\AddressResource;
use Webkul\Partner\Filament\Resources\IndustryResource;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositionResource\Pages\CreateJobPosition;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositionResource\Pages\EditJobPosition;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositionResource\Pages\ListJobPositions;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositionResource\Pages\ViewJobPosition;
use Webkul\Recruitment\Models\JobPosition;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\UserResource;

class JobPositionResource extends Resource
{
    protected static ?string $model = JobPosition::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-position-title'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-position-title-tooltip')),
                                        Select::make('department_id')
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
                                            ->createOptionForm(fn (Schema $schema) => DepartmentResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department-modal-title'));
                                            }),
                                        Select::make('manager_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.manager'))
                                            ->relationship(name: 'manager', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->reactive()
                                            ->live(),
                                        Select::make('company_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company'))
                                            ->relationship(name: 'company', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalIcon('heroicon-o-building-office')
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company-modal-title'));
                                            }),
                                        Select::make('recruiter_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.recruiter'))
                                            ->relationship(name: 'recruiter', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->createOptionForm(fn (Schema $schema) => UserResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalIcon('heroicon-o-building-office')
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company-modal-title'));
                                            }),
                                        Select::make('recruitments_job_position_interviewers')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.interviewers'))
                                            ->relationship(name: 'interviewers', titleAttribute: 'name')
                                            ->searchable()
                                            ->multiple()
                                            ->preload()
                                            ->live()
                                            ->createOptionForm(fn (Schema $schema) => UserResource::form($schema)),
                                        Select::make('address_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-location'))
                                            ->relationship(name: 'address', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema) => AddressResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department-modal-title'))
                                                    ->modalSubmitActionLabel(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department-modal-title'))
                                                    ->modalWidth('2xl');
                                            }),
                                        Select::make('industry_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.industry'))
                                            ->relationship('industry', 'name')
                                            ->searchable()
                                            ->createOptionForm(fn ($form) => IndustryResource::form($form)->columns(2))
                                            ->preload(),
                                    ])->columns(2),
                                Section::make()
                                    ->hiddenLabel()
                                    ->schema([
                                        RichEditor::make('description')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.job-description.fields.job-description'))
                                            ->columnSpanFull(),
                                        RichEditor::make('requirements')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.job-description.fields.job-requirements'))
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        DatePicker::make('date_from')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.date-from'))
                                            ->native(false),
                                        DatePicker::make('date_to')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.date-to'))
                                            ->native(false),
                                    ])->columns(2),
                                Section::make()
                                    ->schema([
                                        TextInput::make('no_of_recruitment')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.recruitment-target'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->default(0),
                                        TextInput::make('no_of_employee')
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('expected_employees')
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('no_of_hired_employee')
                                            ->disabled(),
                                        Select::make('job_position_skills')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.expected-skills'))
                                            ->relationship('skills', 'name')
                                            ->searchable()
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->preload(),
                                        Select::make('employment_type_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.employment-type'))
                                            ->relationship('employmentType', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Toggle::make('is_active')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.status'))
                                            ->inline(false),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.job-position'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.department'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expected_employees')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.expected-employees'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('no_of_employee')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.current-employees'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.status'))
                    ->boolean(),
                TextColumn::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.department')),
                SelectFilter::make('employmentType')
                    ->relationship('employmentType', 'name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.employment-type')),
                SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.company')),
                TernaryFilter::make('is_active')
                    ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.status')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.job-position'))
                            ->icon('heroicon-o-building-office-2'),
                        RelationshipConstraint::make('company')
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
                        RelationshipConstraint::make('department')
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
                        RelationshipConstraint::make('employmentType')
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
                        RelationshipConstraint::make('creator')
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
                        DateConstraint::make('created_at')
                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.table.filters.created-at')),
                        DateConstraint::make('updated_at')
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.actions.delete.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.actions.delete.notification.body'))
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.actions.restore.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.actions.restore.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.force-delete.notification.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.restore.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/job-position.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-briefcase')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.job-position-title')),
                                        TextEntry::make('department.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.department')),
                                        TextEntry::make('manager.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.manager')),
                                        TextEntry::make('company.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.company')),
                                        TextEntry::make('recruiter.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.recruiter')),
                                        TextEntry::make('interviewers.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->listWithLineBreaks()
                                            ->badge()
                                            ->listWithLineBreaks()
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.interviewers')),
                                        TextEntry::make('address.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office-2')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.job-location')),
                                        TextEntry::make('industry.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office-2')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.industry')),
                                    ])->columns(2),
                                Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.title'))
                                    ->schema([
                                        TextEntry::make('description')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.entries.job-description'))
                                            ->placeholder('—')
                                            ->html()
                                            ->columnSpanFull(),
                                        TextEntry::make('requirements')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.entries.job-requirements'))
                                            ->placeholder('—')
                                            ->html()
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make()
                                ->schema([
                                    TextEntry::make('date_from')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.date-from'))
                                        ->placeholder('—')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d') : null)
                                        ->icon('heroicon-o-calendar'),
                                    TextEntry::make('date_to')
                                        ->icon('heroicon-o-calendar')
                                        ->placeholder('—')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d') : null)
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.date-to')),
                                ]),
                            Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.title'))
                                ->schema([
                                    TextEntry::make('expected_employees')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.expected-employees'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-user-group')
                                        ->numeric(),
                                    TextEntry::make('no_of_employee')
                                        ->icon('heroicon-o-user-group')
                                        ->placeholder('—')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.current-employees'))
                                        ->numeric(),
                                    TextEntry::make('no_of_recruitment')
                                        ->icon('heroicon-o-user-group')
                                        ->placeholder('—')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.recruitment-target'))
                                        ->numeric(),
                                    TextEntry::make('employmentType.name')
                                        ->placeholder('—')
                                        ->icon('heroicon-o-briefcase')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.employment-type')),
                                    IconEntry::make('is_active')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.position-status.entries.status')),
                                    TextEntry::make('skills.name')
                                        ->listWithLineBreaks()
                                        ->badge()
                                        ->listWithLineBreaks()
                                        ->label(__('Expected Skills')),
                                ]),
                        ])->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListJobPositions::route('/'),
            'create' => CreateJobPosition::route('/create'),
            'edit'   => EditJobPosition::route('/{record}/edit'),
            'view'   => ViewJobPosition::route('/{record}'),
        ];
    }
}
