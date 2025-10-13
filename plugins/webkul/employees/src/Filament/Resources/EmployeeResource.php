<?php

namespace Webkul\Employee\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Enums\DistanceUnit;
use Webkul\Employee\Enums\Gender;
use Webkul\Employee\Enums\MaritalStatus;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategoryResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositionResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\CreateEmployee;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\EditEmployee;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ListEmployees;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageResume;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageSkill;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ViewEmployee;
use Webkul\Employee\Filament\Resources\EmployeeResource\RelationManagers\ResumeRelationManager;
use Webkul\Employee\Filament\Resources\EmployeeResource\RelationManagers\SkillsRelationManager;
use Webkul\Employee\Models\Calendar;
use Webkul\Employee\Models\Employee;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;

class EmployeeResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Employee::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('employees::filament/resources/employee.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/employee.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/resources/employee.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'department.name',
            'work_email',
            'work_phone',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('employees::filament/resources/employee.global-search.name')       => $record?->name ?? '—',
            __('employees::filament/resources/employee.global-search.department') => $record?->department?->name ?? '—',
            __('employees::filament/resources/employee.global-search.work-email') => $record?->work_email ?? '—',
            __('employees::filament/resources/employee.global-search.work-phone') => $record?->work_phone ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('employees::filament/resources/employee.form.sections.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                            ->columnSpan(1),
                                        TextInput::make('job_title')
                                            ->label(__('employees::filament/resources/employee.form.sections.fields.job-title'))
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                    ]),
                                Group::make()
                                    ->relationship('partner', 'avatar')
                                    ->schema([
                                        FileUpload::make('avatar')
                                            ->image()
                                            ->hiddenLabel()
                                            ->imageResizeMode('cover')
                                            ->imageEditor()
                                            ->avatar()
                                            ->directory('employees/avatar')
                                            ->visibility('public'),
                                    ]),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                TextInput::make('work_email')
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.work-email'))
                                    ->suffixAction(
                                        Action::make('open_mailbox')
                                            ->icon('heroicon-o-envelope')
                                            ->color('gray')
                                            ->action(function (Set $set, ?string $state) {
                                                if ($state && filter_var($state, FILTER_VALIDATE_EMAIL)) {
                                                    $set('work_email', $state);
                                                }
                                            })
                                            ->url(fn (?string $state) => $state ? "mailto:{$state}" : '#')
                                    )
                                    ->email(),
                                Select::make('department_id')
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.department'))
                                    ->relationship(
                                        name: 'department',
                                        titleAttribute: 'complete_name',
                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => DepartmentResource::form($schema)),
                                TextInput::make('mobile_phone')
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.work-mobile'))
                                    ->suffixAction(
                                        Action::make('open_mobile_phone')
                                            ->icon('heroicon-o-phone')
                                            ->color('blue')
                                            ->action(function (Set $set, $state) {
                                                $set('mobile_phone', $state);
                                            })
                                            ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                    )
                                    ->tel(),
                                Select::make('job_id')
                                    ->relationship('job', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.job-position'))
                                    ->createOptionForm(fn (Schema $schema) => JobPositionResource::form($schema)),
                                TextInput::make('work_phone')
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.work-phone'))
                                    ->suffixAction(
                                        Action::make('open_work_phone')
                                            ->icon('heroicon-o-phone')
                                            ->color('blue')
                                            ->action(function (Set $set, $state) {
                                                $set('work_phone', $state);
                                            })
                                            ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                    )
                                    ->tel(),
                                Select::make('parent_id')
                                    ->relationship('parent', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->suffixIcon('heroicon-o-user')
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.manager')),
                                Select::make('employees_employee_categories')
                                    ->multiple()
                                    ->relationship('categories', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.employee-tags'))
                                    ->createOptionForm(fn (Schema $schema) => EmployeeCategoryResource::form($schema)),
                                Select::make('coach_id')
                                    ->searchable()
                                    ->preload()
                                    ->relationship('coach', 'name')
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.coach')),
                            ])
                            ->columns(2),

                    ])
                    ->columns(1)->columnSpanFull(),
                Tabs::make()
                    ->tabs([
                        Tab::make(__('employees::filament/resources/employee.form.tabs.work-information.title'))
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Fieldset::make(__('employees::filament/resources/employee.form.tabs.work-information.fields.location'))
                                                    ->schema([
                                                        Select::make('address_id')
                                                            ->relationship('companyAddress', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->suffixIcon('heroicon-o-map-pin')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.work-address')),
                                                        Select::make('work_location_id')
                                                            ->relationship('workLocation', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.work-location'))
                                                            ->prefixIcon('heroicon-o-map-pin')
                                                            ->createOptionForm(fn (Schema $schema) => WorkLocationResource::form($schema))
                                                            ->editOptionForm(fn (Schema $schema) => WorkLocationResource::form($schema)),
                                                    ])->columns(1),
                                                Fieldset::make(__('employees::filament/resources/employee.form.tabs.work-information.fields.approver'))
                                                    ->schema([
                                                        Select::make('leave_manager_id')
                                                            ->options(fn () => User::pluck('name', 'id'))
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->suffixIcon('heroicon-o-clock')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.time-off')),
                                                        Select::make('attendance_manager_id')
                                                            ->options(fn () => User::pluck('name', 'id'))
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->suffixIcon('heroicon-o-clock')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.attendance-manager')),
                                                    ])->columns(1),
                                                Fieldset::make(__('employees::filament/resources/employee.form.tabs.work-information.fields.schedule'))
                                                    ->schema([
                                                        Select::make('calendar_id')
                                                            ->options(fn () => Calendar::pluck('name', 'id'))
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->suffixIcon('heroicon-o-clock')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.working-hours')),
                                                        Select::make('time_zone')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.time-zone'))
                                                            ->options(function () {
                                                                return collect(timezone_identifiers_list())->mapWithKeys(function ($timezone) {
                                                                    return [$timezone => $timezone];
                                                                });
                                                            })
                                                            ->default(date_default_timezone_get())
                                                            ->preload()
                                                            ->suffixIcon('heroicon-o-clock')
                                                            ->searchable()
                                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('employees::filament/resources/employee.form.tabs.work-information.fields.time-zone-tooltip')),
                                                    ])->columns(1),
                                            ])
                                            ->columnSpan(['lg' => 2]),
                                        Group::make()
                                            ->schema([
                                                Group::make()
                                                    ->schema([
                                                        Fieldset::make(__('employees::filament/resources/employee.form.tabs.work-information.fields.organization-details'))
                                                            ->schema([
                                                                Select::make('company_id')
                                                                    ->relationship('company', 'name')
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->prefixIcon('heroicon-o-building-office')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.company'))
                                                                    ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema)),
                                                                ColorPicker::make('color')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.color'))
                                                                    ->hexColor(),
                                                            ])->columns(1),
                                                    ])
                                                    ->columnSpan(['lg' => 1]),
                                            ])
                                            ->columnSpan(['lg' => 1]),
                                    ])
                                    ->columns(3),
                            ]),
                        Tab::make(__('employees::filament/resources/employee.form.tabs.private-information.title'))
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Group::make()
                                                    ->schema([
                                                        Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.private-contact'))
                                                            ->schema([
                                                                TextInput::make('private_street1')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.street-1')),
                                                                TextInput::make('private_street2')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.street-2')),
                                                                TextInput::make('private_city')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.city')),
                                                                TextInput::make('private_zip')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.postal-code')),
                                                                Select::make('private_country_id')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country'))
                                                                    ->relationship(name: 'country', titleAttribute: 'name')
                                                                    ->afterStateUpdated(fn (Set $set) => $set('private_state_id', null))
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->live(),
                                                                Select::make('private_state_id')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state'))
                                                                    ->relationship(
                                                                        name: 'state',
                                                                        titleAttribute: 'name',
                                                                        modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('country_id', $get('private_country_id')),
                                                                    )
                                                                    ->createOptionForm(function (Schema $schema, Get $get, Set $set) {
                                                                        return $schema
                                                                            ->components([
                                                                                TextInput::make('name')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state-name'))
                                                                                    ->required(),
                                                                                TextInput::make('code')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state-code'))
                                                                                    ->required()
                                                                                    ->unique('states'),
                                                                                Select::make('country_id')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state-country'))
                                                                                    ->relationship('country', 'name')
                                                                                    ->searchable()
                                                                                    ->preload()
                                                                                    ->live()
                                                                                    ->default($get('country_id'))
                                                                                    ->afterStateUpdated(function (Get $get) use ($set) {
                                                                                        $set('private_country_id', $get('country_id'));
                                                                                    }),
                                                                            ]);
                                                                    })
                                                                    ->searchable()
                                                                    ->preload(),
                                                                TextInput::make('private_phone')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.private-phone'))
                                                                    ->suffixAction(
                                                                        Action::make('open_private_phone')
                                                                            ->icon('heroicon-o-phone')
                                                                            ->color('blue')
                                                                            ->action(function (Set $set, $state) {
                                                                                $set('private_phone', $state);
                                                                            })
                                                                            ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                                                    )
                                                                    ->tel(),
                                                                Select::make('bank_account_id')
                                                                    ->relationship('bankAccount', 'account_number')
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->createOptionForm([
                                                                        Group::make()
                                                                            ->schema([
                                                                                TextInput::make('account_number')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-account-number'))
                                                                                    ->required(),
                                                                                Hidden::make('account_holder_name')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-account-holder-name'))
                                                                                    ->default(function (Get $get, $livewire) {
                                                                                        return $livewire->record->user?->name ?? $get('name');
                                                                                    })
                                                                                    ->required(),
                                                                                Hidden::make('partner_id')
                                                                                    ->default(function (Get $get, $livewire) {
                                                                                        return $livewire->record->partner?->id ?? $get('name');
                                                                                    })
                                                                                    ->required(),
                                                                                Hidden::make('creator_id')
                                                                                    ->default(fn () => Auth::user()->id),
                                                                                Select::make('bank_id')
                                                                                    ->relationship('bank', 'name')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank'))
                                                                                    ->searchable()
                                                                                    ->preload()
                                                                                    ->createOptionForm(static::getBankCreateSchema())
                                                                                    ->editOptionForm(static::getBankCreateSchema())
                                                                                    ->createOptionAction(fn (Action $action) => $action->modalHeading(__('employees::filament/resources/employee.form.tabs.private-information.fields.create-bank')))
                                                                                    ->live()
                                                                                    ->required(),
                                                                                Toggle::make('is_active')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.status'))
                                                                                    ->default(true)
                                                                                    ->inline(false),
                                                                                Toggle::make('can_send_money')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.send-money'))
                                                                                    ->default(true)
                                                                                    ->inline(false),

                                                                            ])->columns(2),
                                                                    ])
                                                                    ->createOptionAction(
                                                                        fn (Action $action) => $action
                                                                            ->modalHeading(__('employees::filament/resources/employee.form.tabs.private-information.fields.create-bank-account'))
                                                                            ->modalSubmitActionLabel(__('employees::filament/resources/employee.form.tabs.private-information.fields.create-bank-account'))
                                                                    )
                                                                    ->disabled(fn ($livewire) => ! $livewire->record?->user)
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-account')),
                                                                TextInput::make('private_email')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.private-email'))
                                                                    ->suffixAction(
                                                                        Action::make('open_private_email')
                                                                            ->icon('heroicon-o-envelope')
                                                                            ->color('blue')
                                                                            ->action(function (Set $set, $state) {
                                                                                if (filter_var($state, FILTER_VALIDATE_EMAIL)) {
                                                                                    $set('private_email', $state);
                                                                                }
                                                                            })
                                                                            ->url(fn (?string $state) => $state ? "mailto:{$state}" : '#')
                                                                    )
                                                                    ->email(),
                                                                TextInput::make('private_car_plate')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.private-car-plate')),
                                                                TextInput::make('distance_home_work')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.distance-home-to-work'))
                                                                    ->numeric()
                                                                    ->default(0)
                                                                    ->minValue(0)
                                                                    ->maxValue(99999999999)
                                                                    ->suffix('km'),
                                                                TextInput::make('km_home_work')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.km-home-to-work'))
                                                                    ->numeric()
                                                                    ->default(0)
                                                                    ->minValue(0)
                                                                    ->maxValue(99999999999)
                                                                    ->suffix('km'),
                                                                Select::make('distance_home_work_unit')
                                                                    ->options(DistanceUnit::options())
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.distance-unit')),
                                                            ])->columns(2),
                                                        Group::make()
                                                            ->schema([
                                                                Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.emergency-contact'))
                                                                    ->schema([
                                                                        TextInput::make('emergency_contact')
                                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.contact-name')),
                                                                        TextInput::make('emergency_phone')
                                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.contact-phone'))
                                                                            ->suffixAction(
                                                                                Action::make('open_emergency_phone')
                                                                                    ->icon('heroicon-o-phone')
                                                                                    ->color('blue')
                                                                                    ->action(function (Set $set, $state) {
                                                                                        $set('emergency_phone', $state);
                                                                                    })
                                                                                    ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                                                            )
                                                                            ->tel(),
                                                                    ])->columns(2),
                                                            ])
                                                            ->columnSpan(['lg' => 1]),
                                                        Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.family-status'))
                                                            ->schema([
                                                                Select::make('marital')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.marital-status'))
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->default(MaritalStatus::Single->value)
                                                                    ->options(MaritalStatus::options())
                                                                    ->live(),
                                                                TextInput::make('spouse_complete_name')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.spouse-name'))
                                                                    ->hidden(fn (Get $get) => $get('marital') === MaritalStatus::Single->value)
                                                                    ->dehydrated(fn (Get $get) => $get('marital') !== MaritalStatus::Single->value)
                                                                    ->required(fn (Get $get) => $get('marital') !== MaritalStatus::Single->value),
                                                                DatePicker::make('spouse_birthdate')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.spouse-birthday'))
                                                                    ->native(false)
                                                                    ->suffixIcon('heroicon-o-calendar')
                                                                    ->disabled(fn (Get $get) => $get('marital') === MaritalStatus::Single->value)
                                                                    ->hidden(fn (Get $get) => $get('marital') === MaritalStatus::Single->value)
                                                                    ->dehydrated(fn (Get $get) => $get('marital') !== MaritalStatus::Single->value),
                                                                TextInput::make('children')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.number-of-children'))
                                                                    ->numeric()
                                                                    ->minValue(0)
                                                                    ->maxValue(99999999999)
                                                                    ->disabled(fn (Get $get) => $get('marital') === MaritalStatus::Single->value)
                                                                    ->hidden(fn (Get $get) => $get('marital') === MaritalStatus::Single->value)
                                                                    ->dehydrated(fn (Get $get) => $get('marital') !== MaritalStatus::Single->value),
                                                            ])->columns(2),
                                                        Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.education'))
                                                            ->schema([
                                                                Select::make('certificate')
                                                                    ->options([
                                                                        'graduate' => __('employees::filament/resources/employee.form.tabs.private-information.fields.graduated'),
                                                                        'bachelor' => __('employees::filament/resources/employee.form.tabs.private-information.fields.bachelor'),
                                                                        'master'   => __('employees::filament/resources/employee.form.tabs.private-information.fields.master'),
                                                                        'doctor'   => __('employees::filament/resources/employee.form.tabs.private-information.fields.doctor'),
                                                                        'other'    => __('employees::filament/resources/employee.form.tabs.private-information.fields.other'),
                                                                    ])
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.certificate-level')),
                                                                TextInput::make('study_field')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.field-of-study')),
                                                                TextInput::make('study_school')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.school')),
                                                            ])->columns(1),

                                                    ]),
                                            ])
                                            ->columnSpan(['lg' => 2]),
                                        Group::make()
                                            ->schema([
                                                Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.citizenship'))
                                                    ->schema([
                                                        Select::make('country_id')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country'))
                                                            ->relationship(name: 'country', titleAttribute: 'name')
                                                            ->createOptionForm([
                                                                TextInput::make('name')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country-name'))
                                                                    ->required(),
                                                                TextInput::make('code')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country-code'))
                                                                    ->required()
                                                                    ->rules('max:2'),
                                                                Toggle::make('state_required')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country-state-required'))
                                                                    ->required(),
                                                                Toggle::make('zip_required')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country-zip-required'))
                                                                    ->required(),
                                                            ])
                                                            ->createOptionAction(
                                                                fn (Action $action) => $action
                                                                    ->modalHeading(__('employees::filament/resources/employee.form.tabs.private-information.fields.create-country'))
                                                                    ->modalSubmitActionLabel(__('employees::filament/resources/employee.form.tabs.private-information.fields.create-country'))
                                                                    ->modalWidth('lg')
                                                            )
                                                            ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                                                            ->searchable()
                                                            ->preload()
                                                            ->live(),
                                                        Select::make('state_id')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state'))
                                                            ->relationship(
                                                                name: 'state',
                                                                titleAttribute: 'name',
                                                                modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                                                            )
                                                            ->createOptionForm([
                                                                TextInput::make('name')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state-name'))
                                                                    ->required()
                                                                    ->maxLength(255),
                                                                TextInput::make('code')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state-code'))
                                                                    ->required()
                                                                    ->maxLength(255),
                                                            ])
                                                            ->createOptionAction(
                                                                fn (Action $action) => $action
                                                                    ->modalHeading(__('employees::filament/resources/employee.form.tabs.private-information.fields.create-state'))
                                                                    ->modalSubmitActionLabel(__('employees::filament/resources/employee.form.tabs.private-information.fields.create-state'))
                                                                    ->modalWidth('lg')
                                                            )
                                                            ->searchable()
                                                            ->preload()
                                                            ->required(fn (Get $get) => Country::find($get('country_id'))?->state_required),
                                                        TextInput::make('identification_id')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.identification-id')),
                                                        TextInput::make('ssnid')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.ssnid')),
                                                        TextInput::make('sinid')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.sinid')),
                                                        TextInput::make('passport_id')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.passport-id')),
                                                        Select::make('gender')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.gender'))
                                                            ->searchable()
                                                            ->preload()
                                                            ->options(Gender::options()),
                                                        DatePicker::make('birthday')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.date-of-birth'))
                                                            ->suffixIcon('heroicon-o-calendar')
                                                            ->native(false)
                                                            ->maxDate(now()),
                                                        Select::make('country_of_birth')
                                                            ->relationship('countryOfBirth', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country-of-birth')),

                                                    ])->columns(1),
                                                Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.work-permit'))
                                                    ->schema([
                                                        TextInput::make('visa_no')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.visa-number')),
                                                        TextInput::make('permit_no')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.work-permit-no')),
                                                        DatePicker::make('visa_expire')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.visa-expiration-date'))
                                                            ->suffixIcon('heroicon-o-calendar')
                                                            ->native(false),
                                                        DatePicker::make('work_permit_expiration_date')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.work-permit-expiration-date'))
                                                            ->suffixIcon('heroicon-o-calendar')
                                                            ->native(false),
                                                        FileUpload::make('work_permit')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.work-permit'))
                                                            ->panelAspectRatio('4:1')
                                                            ->panelLayout('integrated')
                                                            ->acceptedFileTypes([
                                                                'image/*',
                                                                'application/pdf',
                                                            ])
                                                            ->directory('employees/work-permit')
                                                            ->visibility('public'),
                                                    ])->columns(1),
                                            ])
                                            ->columnSpan(['lg' => 1]),
                                    ])
                                    ->columns(3),
                            ]),
                        Tab::make(__('employees::filament/resources/employee.form.tabs.settings.title'))
                            ->icon('heroicon-o-cog-8-tooth')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Fieldset::make(__('employees::filament/resources/employee.form.tabs.settings.fields.employment-status'))
                                                    ->schema([
                                                        Toggle::make('is_active')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.active-employee'))
                                                            ->default(true)
                                                            ->inline(false),
                                                        Toggle::make('is_flexible')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.flexible-work-arrangement'))
                                                            ->inline(false),
                                                        Toggle::make('is_fully_flexible')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.fully-flexible-schedule'))
                                                            ->inline(false),
                                                        Toggle::make('work_permit_scheduled_activity')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.work-permit-scheduled-activity')),
                                                        Select::make('user_id')
                                                            ->relationship(name: 'user', titleAttribute: 'name', modifyQueryUsing: fn ($query) => $query->withTrashed())
                                                            ->getOptionLabelFromRecordUsing(function ($record, $livewire) {
                                                                $label = $record->name;

                                                                if ($record->trashed()) {
                                                                    $label .= ' (Deleted)';
                                                                } elseif ($record->employee && $record->id !== $livewire->record?->user_id) {
                                                                    $label .= ' (Assigned)';
                                                                }

                                                                return $label;
                                                            })
                                                            ->disableOptionWhen(function ($value, $livewire) {
                                                                $user = User::withTrashed()->find($value);

                                                                if (! $user) {
                                                                    return false;
                                                                }

                                                                $currentEmployeeUserId = $livewire->record?->user_id ?? null;

                                                                return ($user->trashed() || $user->employee()->exists())
                                                                    && $user->id !== $currentEmployeeUserId;
                                                            })
                                                            ->searchable()
                                                            ->preload()
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.related-user'))
                                                            ->prefixIcon('heroicon-o-user')
                                                            ->createOptionForm(fn (Schema $schema) => UserResource::form($schema))
                                                            ->createOptionAction(
                                                                fn (Action $action, Get $get) => $action
                                                                    ->fillForm(function (array $arguments) use ($get): array {
                                                                        return [
                                                                            'name'  => $get('name'),
                                                                            'email' => $get('work_email'),
                                                                        ];
                                                                    })
                                                                    ->modalHeading(__('employees::filament/resources/employee.form.tabs.settings.fields.create-user'))
                                                                    ->modalSubmitActionLabel(__('employees::filament/resources/employee.form.tabs.settings.fields.create-user'))
                                                                    ->action(function (array $data, $component) {
                                                                        $user = User::create($data);

                                                                        $partner = $user->partner()->create([
                                                                            'creator_id' => Auth::user()->id,
                                                                            'user_id'    => $user->id,
                                                                            'company_id' => $data['default_company_id'] ?? null,
                                                                            'avatar'     => $data['avatar'] ?? null,
                                                                            ...$data,
                                                                        ]);

                                                                        $user->update([
                                                                            'partner_id' => $partner->id,
                                                                        ]);

                                                                        $component->state($user->id);

                                                                        return $user;
                                                                    })
                                                            ),
                                                        Select::make('departure_reason_id')
                                                            ->relationship('departureReason', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.departure-reason'))
                                                            ->createOptionForm(fn (Schema $schema) => DepartureReasonResource::form($schema)),
                                                        DatePicker::make('departure_date')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.departure-date'))
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) => $get('departure_reason_id') === null)
                                                            ->disabled(fn (Get $get) => $get('departure_reason_id') === null)
                                                            ->required(fn (Get $get) => $get('departure_reason_id') !== null),
                                                        Textarea::make('departure_description')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.departure-description'))
                                                            ->hidden(fn (Get $get) => $get('departure_reason_id') === null)
                                                            ->disabled(fn (Get $get) => $get('departure_reason_id') === null)
                                                            ->required(fn (Get $get) => $get('departure_reason_id') !== null),
                                                    ])->columns(2),
                                                Fieldset::make(__('employees::filament/resources/employee.form.tabs.settings.fields.additional-information'))
                                                    ->schema([
                                                        TextInput::make('lang')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.primary-language')),
                                                        Textarea::make('additional_note')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.additional-notes'))
                                                            ->rows(3),
                                                        Textarea::make('notes')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.notes')),
                                                        ...static::getCustomFormFields(),
                                                    ])->columns(2),
                                            ])
                                            ->columnSpan(['lg' => 2]),
                                        Group::make()
                                            ->schema([
                                                Fieldset::make(__('employees::filament/resources/employee.form.tabs.settings.fields.attendance-point-of-sale'))
                                                    ->schema([
                                                        TextInput::make('barcode')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.badge-id'))
                                                            ->prefixIcon('heroicon-o-qr-code')
                                                            ->suffixAction(
                                                                Action::make('generate_bar_code')
                                                                    ->icon('heroicon-o-plus-circle')
                                                                    ->color('gray')
                                                                    ->action(function (Set $set) {
                                                                        $barcode = strtoupper(bin2hex(random_bytes(4)));

                                                                        $set('barcode', $barcode);
                                                                    })
                                                            ),
                                                        TextInput::make('pin')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.pin')),
                                                    ])->columns(1),
                                            ])
                                            ->columnSpan(['lg' => 1]),
                                    ])
                                    ->columns(3),
                            ]),
                    ])
                    ->columnSpan('full')
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('partner.avatar')
                        ->imageHeight(150)
                        ->width(200),
                    Stack::make([
                        TextColumn::make('name')
                            ->label(__('employees::filament/resources/employee.table.columns.name'))
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->sortable(),
                        Stack::make([
                            TextColumn::make('job_title')
                                ->icon('heroicon-m-briefcase')
                                ->searchable()
                                ->sortable()
                                ->label(__('employees::filament/resources/employee.table.columns.job-title')),
                        ])
                            ->visible(fn ($record) => filled($record->job_title)),
                        Stack::make([
                            TextColumn::make('work_email')
                                ->icon('heroicon-o-envelope')
                                ->searchable()
                                ->sortable()
                                ->label(__('employees::filament/resources/employee.table.columns.work-email'))
                                ->color('gray')
                                ->limit(20),
                        ])
                            ->visible(fn ($record) => filled($record->work_email)),
                        Stack::make([
                            TextColumn::make('work_phone')
                                ->icon('heroicon-o-phone')
                                ->searchable()
                                ->label(__('employees::filament/resources/employee.table.columns.work-phone'))
                                ->color('gray')
                                ->limit(30)
                                ->sortable(),
                        ])
                            ->visible(fn ($record) => filled($record->work_phone)),
                        Stack::make([
                            TextColumn::make('categories.name')
                                ->label(__('employees::filament/resources/employee.table.columns.categories'))
                                ->badge()
                                ->state(function (Employee $record): array {
                                    return $record->categories->map(fn ($category) => [
                                        'label' => $category->name,
                                        'color' => $category->color ?? '#808080',
                                    ])->toArray();
                                })
                                ->formatStateUsing(fn ($state) => $state['label'])
                                ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                ->weight(FontWeight::Bold),
                        ])
                            ->visible(fn ($record): bool => (bool) $record->categories->count()),
                    ])->space(1),
                ])->space(4),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 4,
            ])
            ->paginated([
                18,
                36,
                72,
                'all',
            ])
            ->filtersFormColumns(3)
            ->filters([
                SelectFilter::make('skills')
                    ->relationship('skills.skill', 'name')
                    ->searchable()
                    ->multiple()
                    ->label(__('employees::filament/resources/employee.table.filters.skills'))
                    ->preload(),
                SelectFilter::make('resumes')
                    ->relationship('resumes', 'name')
                    ->searchable()
                    ->label(__('employees::filament/resources/employee.table.filters.resumes'))
                    ->multiple()
                    ->preload(),
                SelectFilter::make('time_zone')
                    ->options(function () {
                        return collect(timezone_identifiers_list())->mapWithKeys(function ($timezone) {
                            return [$timezone => $timezone];
                        });
                    })
                    ->searchable()
                    ->label(__('employees::filament/resources/employee.table.filters.timezone'))
                    ->multiple()
                    ->preload(),
                QueryBuilder::make()
                    ->constraintPickerColumns(5)
                    ->constraints([
                        TextConstraint::make('job_title')
                            ->label(__('employees::filament/resources/employee.table.filters.job-title'))
                            ->icon('heroicon-o-user-circle'),
                        DateConstraint::make('birthday')
                            ->label(__('employees::filament/resources/employee.table.filters.birthdate'))
                            ->icon('heroicon-o-cake'),
                        TextConstraint::make('work_email')
                            ->label(__('employees::filament/resources/employee.table.filters.work-email'))
                            ->icon('heroicon-o-at-symbol'),
                        TextConstraint::make('mobile_phone')
                            ->label(__('employees::filament/resources/employee.table.filters.mobile-phone'))
                            ->icon('heroicon-o-phone'),
                        TextConstraint::make('work_phone')
                            ->label(__('employees::filament/resources/employee.table.filters.work-phone'))
                            ->icon('heroicon-o-phone'),
                        TextConstraint::make('is_flexible')
                            ->label(__('employees::filament/resources/employee.table.filters.is-flexible'))
                            ->icon('heroicon-o-cube'),
                        TextConstraint::make('is_fully_flexible')
                            ->label(__('employees::filament/resources/employee.table.filters.is-fully-flexible'))
                            ->icon('heroicon-o-cube'),
                        TextConstraint::make('is_active')
                            ->label(__('employees::filament/resources/employee.table.filters.is-active'))
                            ->icon('heroicon-o-cube'),
                        TextConstraint::make('work_permit_scheduled_activity')
                            ->label(__('employees::filament/resources/employee.table.filters.work-permit-scheduled-activity'))
                            ->icon('heroicon-o-cube'),
                        TextConstraint::make('emergency_contact')
                            ->label(__('employees::filament/resources/employee.table.filters.emergency-contact'))
                            ->icon('heroicon-o-phone'),
                        TextConstraint::make('emergency_phone')
                            ->label(__('employees::filament/resources/employee.table.filters.emergency-phone'))
                            ->icon('heroicon-o-phone'),
                        TextConstraint::make('private_phone')
                            ->label(__('employees::filament/resources/employee.table.filters.private-phone'))
                            ->icon('heroicon-o-phone'),
                        TextConstraint::make('private_email')
                            ->label(__('employees::filament/resources/employee.table.filters.private-email'))
                            ->icon('heroicon-o-at-symbol'),
                        TextConstraint::make('private_car_plate')
                            ->label(__('employees::filament/resources/employee.table.filters.private-car-plate'))
                            ->icon('heroicon-o-clipboard-document'),
                        TextConstraint::make('distance_home_work')
                            ->label(__('employees::filament/resources/employee.table.filters.distance-home-work'))
                            ->icon('heroicon-o-map'),
                        TextConstraint::make('km_home_work')
                            ->label(__('employees::filament/resources/employee.table.filters.km-home-work'))
                            ->icon('heroicon-o-map'),
                        TextConstraint::make('distance_home_work_unit')
                            ->label(__('employees::filament/resources/employee.table.filters.distance-home-work-unit'))
                            ->icon('heroicon-o-map'),
                        TextConstraint::make('marital')
                            ->label(__('employees::filament/resources/employee.table.filters.marital-status'))
                            ->icon('heroicon-o-user'),
                        TextConstraint::make('spouse_complete_name')
                            ->label(__('employees::filament/resources/employee.table.filters.spouse-name'))
                            ->icon('heroicon-o-user'),
                        DateConstraint::make('spouse_birthdate')
                            ->label(__('employees::filament/resources/employee.table.filters.spouse-birthdate'))
                            ->icon('heroicon-o-cake'),
                        TextConstraint::make('certificate')
                            ->label(__('employees::filament/resources/employee.table.filters.certificate'))
                            ->icon('heroicon-o-document'),
                        TextConstraint::make('study_field')
                            ->label(__('employees::filament/resources/employee.table.filters.study-field'))
                            ->icon('heroicon-o-academic-cap'),
                        TextConstraint::make('study_school')
                            ->label(__('employees::filament/resources/employee.table.filters.study-school'))
                            ->icon('heroicon-o-academic-cap'),
                        TextConstraint::make('identification_id')
                            ->label(__('employees::filament/resources/employee.table.filters.identification-id'))
                            ->icon('heroicon-o-credit-card'),
                        TextConstraint::make('ssnid')
                            ->label(__('employees::filament/resources/employee.table.filters.ssnid'))
                            ->icon('heroicon-o-credit-card'),
                        TextConstraint::make('sinid')
                            ->label(__('employees::filament/resources/employee.table.filters.sinid'))
                            ->icon('heroicon-o-credit-card'),
                        TextConstraint::make('passport_id')
                            ->label(__('employees::filament/resources/employee.table.filters.passport-id'))
                            ->icon('heroicon-o-credit-card'),
                        TextConstraint::make('gender')
                            ->label(__('employees::filament/resources/employee.table.filters.gender'))
                            ->icon('heroicon-o-user'),
                        NumberConstraint::make('children')
                            ->label(__('employees::filament/resources/employee.table.filters.children'))
                            ->icon('heroicon-o-user'),
                        TextConstraint::make('visa_no')
                            ->label(__('employees::filament/resources/employee.table.filters.visa-no'))
                            ->icon('heroicon-o-credit-card'),
                        TextConstraint::make('permit_no')
                            ->label(__('employees::filament/resources/employee.table.filters.permit-no'))
                            ->icon('heroicon-o-credit-card'),
                        TextConstraint::make('lang')
                            ->label(__('employees::filament/resources/employee.table.filters.language'))
                            ->icon('heroicon-o-language'),
                        TextConstraint::make('additional_note')
                            ->label(__('employees::filament/resources/employee.table.filters.additional-note'))
                            ->icon('heroicon-o-language'),
                        TextConstraint::make('notes')
                            ->label(__('employees::filament/resources/employee.table.filters.notes'))
                            ->icon('heroicon-o-language'),
                        TextConstraint::make('barcode')
                            ->label(__('employees::filament/resources/employee.table.filters.barcode'))
                            ->icon('heroicon-o-qr-code'),
                        DateConstraint::make('visa_expire')
                            ->label(__('employees::filament/resources/employee.table.filters.visa-expire'))
                            ->icon('heroicon-o-credit-card'),
                        DateConstraint::make('work_permit_expiration_date')
                            ->label(__('employees::filament/resources/employee.table.filters.work-permit-expiration-date'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('departure_date')
                            ->label(__('employees::filament/resources/employee.table.filters.departure-date'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('departure_description')
                            ->label(__('employees::filament/resources/employee.table.filters.departure-description'))
                            ->icon('heroicon-o-cube'),
                        DateConstraint::make('created_at')
                            ->label(__('employees::filament/resources/employee.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/resources/employee.table.filters.updated-at')),
                        RelationshipConstraint::make('company')
                            ->label(__('employees::filament/resources/employee.table.filters.company'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('creator')
                            ->label(__('employees::filament/resources/employee.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('calendar')
                            ->label(__('employees::filament/resources/employee.table.filters.calendar'))
                            ->icon('heroicon-o-calendar')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('department')
                            ->label(__('employees::filament/resources/employee.table.filters.department'))
                            ->multiple()
                            ->icon('heroicon-o-building-office-2')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('job')
                            ->label(__('employees::filament/resources/employee.table.filters.job'))
                            ->icon('heroicon-o-briefcase')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partner')
                            ->label(__('employees::filament/resources/employee.table.filters.partner'))
                            ->icon('heroicon-o-user-group')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('leaveManager')
                            ->label(__('employees::filament/resources/employee.table.filters.leave-approvers'))
                            ->icon('heroicon-o-user-group')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('attendanceManager')
                            ->label(__('employees::filament/resources/employee.table.filters.attendance'))
                            ->icon('heroicon-o-user-group')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('workLocation')
                            ->label(__('employees::filament/resources/employee.table.filters.work-location'))
                            ->multiple()
                            ->icon('heroicon-o-map-pin')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('parent')
                            ->label(__('employees::filament/resources/employee.table.filters.manager'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('coach')
                            ->label(__('employees::filament/resources/employee.table.filters.coach'))
                            ->multiple()
                            ->icon('heroicon-o-user')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('privateState')
                            ->label(__('employees::filament/resources/employee.table.filters.private-state'))
                            ->multiple()
                            ->icon('heroicon-o-map-pin')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('privateCountry')
                            ->label(__('employees::filament/resources/employee.table.filters.private-country'))
                            ->icon('heroicon-o-map-pin')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('country')
                            ->label(__('employees::filament/resources/employee.table.filters.country'))
                            ->icon('heroicon-o-map-pin')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('state')
                            ->label(__('employees::filament/resources/employee.table.filters.state'))
                            ->icon('heroicon-o-map-pin')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('countryOfBirth')
                            ->label(__('employees::filament/resources/employee.table.filters.country-of-birth'))
                            ->multiple()
                            ->icon('heroicon-o-calendar')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('bankAccount')
                            ->label(__('employees::filament/resources/employee.table.filters.bank-account'))
                            ->multiple()
                            ->icon('heroicon-o-banknotes')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('account_holder_name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('departureReason')
                            ->label(__('employees::filament/resources/employee.table.filters.departure-reason'))
                            ->icon('heroicon-o-fire')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('employmentType')
                            ->label(__('employees::filament/resources/employee.table.filters.employee-type'))
                            ->multiple()
                            ->icon('heroicon-o-academic-cap')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('categories')
                            ->label(__('employees::filament/resources/employee.table.filters.tags'))
                            ->icon('heroicon-o-tag')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('employees::filament/resources/employee.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('employees::filament/resources/employee.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('parent.name')
                    ->label(__('employees::filament/resources/employee.table.groups.manager'))
                    ->collapsible(),
                Tables\Grouping\Group::make('coach.name')
                    ->label(__('employees::filament/resources/employee.table.groups.coach'))
                    ->collapsible(),
                Tables\Grouping\Group::make('department.complete_name')
                    ->label(__('employees::filament/resources/employee.table.groups.department'))
                    ->collapsible(),
                Tables\Grouping\Group::make('employmentType.name')
                    ->label(__('employees::filament/resources/employee.table.groups.employment-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('categories.name')
                    ->label(__('employees::filament/resources/employee.table.groups.tags'))
                    ->collapsible(),
                Tables\Grouping\Group::make('departureReason.name')
                    ->label(__('employees::filament/resources/employee.table.groups.departure-reason'))
                    ->collapsible(),
                Tables\Grouping\Group::make('privateState.name')
                    ->label(__('employees::filament/resources/employee.table.groups.private-state'))
                    ->collapsible(),
                Tables\Grouping\Group::make('privateCountry.name')
                    ->label(__('employees::filament/resources/employee.table.groups.private-country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('country.name')
                    ->label(__('employees::filament/resources/employee.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state.name')
                    ->label(__('employees::filament/resources/employee.table.groups.state'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('employees::filament/resources/employee.table.groups.created-at'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('employees::filament/resources/employee.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->defaultSort('name')
            ->persistSortInSession()
            ->recordActions([
                ViewAction::make()
                    ->outlined(),
                EditAction::make()
                    ->outlined(),
                RestoreAction::make()
                    ->outlined()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee.table.actions.restore.notification.title'))
                            ->body(__('employees::filament/resources/employee.table.actions.restore.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/resources/employee.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/employee.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/resources/employee.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('employees::filament/resources/employee.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('employees::filament/resources/employee.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/employee.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('employees::filament/resources/employee.table.bulk-actions.force-delete.notification.success.body'))
                        ),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['categories']));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(['default' => 2])
                            ->schema([
                                Group::make([
                                    TextEntry::make('name')
                                        ->label(__('employees::filament/resources/employee.infolist.sections.entries.name'))
                                        ->weight(FontWeight::Bold)
                                        ->placeholder('—')
                                        ->size(TextSize::Large),
                                    TextEntry::make('job_title')
                                        ->placeholder('—')
                                        ->label(__('employees::filament/resources/employee.infolist.sections.entries.job-title')),
                                ])->columnSpan(1),
                                Group::make([
                                    ImageEntry::make('partner.avatar')
                                        ->hiddenLabel()
                                        ->imageHeight(140)
                                        ->circular(),
                                ])->columnSpan(1),
                            ]),
                        Grid::make(['default' => 2])
                            ->schema([
                                TextEntry::make('work_email')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.work-email'))
                                    ->placeholder('—')
                                    ->url(fn (?string $state) => $state ? "mailto:{$state}" : '#')
                                    ->icon('heroicon-o-envelope')
                                    ->iconPosition(IconPosition::Before),
                                TextEntry::make('department.complete_name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.department')),
                                TextEntry::make('mobile_phone')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.work-mobile'))
                                    ->placeholder('—')
                                    ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                    ->icon('heroicon-o-phone')
                                    ->iconPosition(IconPosition::Before),
                                TextEntry::make('job.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.job-position')),
                                TextEntry::make('work_phone')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.work-phone'))
                                    ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                    ->icon('heroicon-o-phone')
                                    ->iconPosition(IconPosition::Before),
                                TextEntry::make('parent.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.manager')),
                                TextEntry::make('categories.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.employee-tags'))
                                    ->placeholder('—')
                                    ->state(function (Employee $record): array {
                                        return $record->categories->map(fn ($category) => [
                                            'label' => $category->name,
                                            'color' => $category->color ?? '#808080',
                                        ])->toArray();
                                    })
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state['label'])
                                    ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                    ->listWithLineBreaks(),
                                TextEntry::make('coach.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.coach')),
                            ]),
                    ])->columnSpanFull(),

                Tabs::make()
                    ->tabs([
                        Tab::make(__('employees::filament/resources/employee.infolist.tabs.work-information.title'))
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Grid::make(['default' => 3])
                                    ->schema([
                                        Group::make([
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.location'))
                                                ->schema([
                                                    TextEntry::make('companyAddress.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.work-address'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    TextEntry::make('workLocation.name')
                                                        ->placeholder('—')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.work-location'))
                                                        ->icon('heroicon-o-building-office'),
                                                ]),
                                            Fieldset::make('Approvers')
                                                ->schema([
                                                    TextEntry::make('leaveManager.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.time-off'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-user-group'),
                                                    TextEntry::make('attendanceManager.name')
                                                        ->placeholder('—')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.attendance-manager'))
                                                        ->icon('heroicon-o-user-group'),
                                                ]),
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.schedule'))
                                                ->schema([
                                                    TextEntry::make('calendar.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.working-hours'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-clock'),
                                                    TextEntry::make('time_zone')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.timezone'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-clock'),
                                                ]),
                                        ])->columnSpan(2),
                                        Group::make([
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.organization-details'))
                                                ->schema([
                                                    TextEntry::make('company.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.company'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-briefcase'),
                                                    ColorEntry::make('color')
                                                        ->placeholder('—')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.color')),
                                                ]),
                                        ])->columnSpan(1),
                                    ]),
                            ]),
                        Tab::make(__('employees::filament/resources/employee.infolist.tabs.private-information.title'))
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Grid::make(['default' => 3])
                                    ->schema([
                                        Group::make([
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-contact'))
                                                ->schema([
                                                    TextEntry::make('private_street1')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.street-address'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    TextEntry::make('private_street2')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.street-address-line-2'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    TextEntry::make('private_city')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.city'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-building-office'),
                                                    TextEntry::make('private_zip')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.post-code'))
                                                        ->icon('heroicon-o-document-text'),
                                                    TextEntry::make('privateCountry.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.country'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-globe-alt'),
                                                    TextEntry::make('privateState.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.state'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    TextEntry::make('private_phone')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-phone'))
                                                        ->placeholder('—')
                                                        ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                                        ->icon('heroicon-o-phone'),
                                                    TextEntry::make('private_email')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-email'))
                                                        ->placeholder('—')
                                                        ->url(fn (?string $state) => $state ? "mailto:{$state}" : '#')
                                                        ->icon('heroicon-o-envelope'),
                                                    TextEntry::make('private_car_plate')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-car-plate'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-rectangle-stack'),
                                                    TextEntry::make('distance_home_work')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.distance-home-to-work'))
                                                        ->placeholder('—')
                                                        ->suffix('km')
                                                        ->icon('heroicon-o-map'),
                                                ]),
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.emergency-contact'))
                                                ->schema([
                                                    TextEntry::make('emergency_contact')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.contact-name'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-user'),
                                                    TextEntry::make('emergency_phone')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.contact-phone'))
                                                        ->placeholder('—')
                                                        ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                                        ->icon('heroicon-o-phone'),
                                                ]),
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit'))
                                                ->schema([
                                                    TextEntry::make('visa_no')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.visa-number'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-document-text')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.visa-number-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('permit_no')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-number'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-rectangle-stack')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-number-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('visa_expire')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.visa-expiration-date'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-calendar-days')
                                                        ->date('F j, Y')
                                                        ->color(
                                                            fn ($record) => $record->visa_expire && now()->diffInDays($record->visa_expire, false) <= 30
                                                                ? 'danger'
                                                                : 'success'
                                                        ),
                                                    TextEntry::make('work_permit_expiration_date')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-expiration-date'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-calendar-days')
                                                        ->date('F j, Y')
                                                        ->color(
                                                            fn ($record) => $record->work_permit_expiration_date && now()->diffInDays($record->work_permit_expiration_date, false) <= 30
                                                                ? 'danger'
                                                                : 'success'
                                                        ),
                                                    ImageEntry::make('work_permit')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-document'))
                                                        ->columnSpanFull()
                                                        ->placeholder('—')
                                                        ->imageHeight(200),
                                                ]),
                                        ])->columnSpan(2),
                                        Group::make([
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.citizenship'))
                                                ->columns(1)
                                                ->schema([
                                                    TextEntry::make('country.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.country'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-globe-alt'),
                                                    TextEntry::make('state.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.state'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    TextEntry::make('identification_id')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.identification-id'))
                                                        ->icon('heroicon-o-document-text')
                                                        ->placeholder('—')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.identification-id-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('ssnid')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.ssnid'))
                                                        ->icon('heroicon-o-document-check')
                                                        ->placeholder('—')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.ssnid-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('sinid')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.sinid'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-document')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.sinid-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('passport_id')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.passport-id'))
                                                        ->icon('heroicon-o-identification')
                                                        ->copyable()
                                                        ->placeholder('—')
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.passport-id-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('gender')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.gender'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-user')
                                                        ->badge()
                                                        ->color(fn (string $state): string => match ($state) {
                                                            'male'   => 'info',
                                                            'female' => 'success',
                                                            default  => 'warning',
                                                        }),
                                                    TextEntry::make('birthday')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.date-of-birth'))
                                                        ->icon('heroicon-o-calendar')
                                                        ->placeholder('—')
                                                        ->date('F j, Y'),
                                                    TextEntry::make('countryOfBirth.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.country-of-birth'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-globe-alt'),
                                                    TextEntry::make('country.phone_code')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.phone-code'))
                                                        ->icon('heroicon-o-phone')
                                                        ->placeholder('—')
                                                        ->prefix('+'),
                                                ]),
                                        ])->columnSpan(1),
                                    ]),
                            ]),
                        Tab::make(__('employees::filament/resources/employee.infolist.tabs.settings.title'))
                            ->icon('heroicon-o-cog-8-tooth')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.settings.entries.employee-settings'))
                                                    ->schema([
                                                        IconEntry::make('is_active')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.active-employee'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        IconEntry::make('is_flexible')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.flexible-work-arrangement'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        IconEntry::make('is_fully_flexible')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.fully-flexible-schedule'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        IconEntry::make('work_permit_scheduled_activity')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.work-permit-scheduled-activity'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        TextEntry::make('user.name')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.related-user'))
                                                            ->placeholder('—')
                                                            ->icon('heroicon-o-user'),
                                                        TextEntry::make('departureReason.name')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.departure-reason')),
                                                        TextEntry::make('departure_date')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.departure-date'))
                                                            ->icon('heroicon-o-calendar-days'),
                                                        TextEntry::make('departure_description')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.departure-description')),
                                                    ])
                                                    ->columns(2),
                                                Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.settings.entries.additional-information'))
                                                    ->schema([
                                                        TextEntry::make('lang')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.primary-language')),
                                                        TextEntry::make('additional_note')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.additional-notes'))
                                                            ->columnSpanFull(),
                                                        TextEntry::make('notes')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.notes')),
                                                    ])
                                                    ->columns(2),
                                            ])
                                            ->columnSpan(['lg' => 2]),
                                        Group::make()
                                            ->schema([
                                                Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.settings.entries.attendance-point-of-sale'))
                                                    ->schema([
                                                        TextEntry::make('barcode')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.badge-id'))
                                                            ->icon('heroicon-o-qr-code'),
                                                        TextEntry::make('pin')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.pin')),
                                                    ])
                                                    ->columns(1),
                                            ])
                                            ->columnSpan(['lg' => 1]),
                                    ])
                                    ->columns(3),

                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpan('full'),
            ]);
    }

    public static function getBankCreateSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    TextInput::make('name')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-name'))
                        ->required(),
                    TextInput::make('code')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-code'))
                        ->required(),
                    TextInput::make('email')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-email'))
                        ->email()
                        ->required(),
                    TextInput::make('phone')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-phone-number'))
                        ->tel(),
                    TextInput::make('street1')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-street-1')),
                    TextInput::make('street2')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-street-2')),
                    TextInput::make('city')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-city')),
                    TextInput::make('zip')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-zipcode')),
                    Select::make('country_id')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-country'))
                        ->relationship(name: 'country', titleAttribute: 'name')
                        ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                        ->searchable()
                        ->preload()
                        ->live(),
                    Select::make('state_id')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-state'))
                        ->relationship(
                            name: 'state',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                        )
                        ->searchable()
                        ->preload()
                        ->required(fn (Get $get) => Country::find($get('country_id'))?->state_required),
                    Hidden::make('creator_id')
                        ->default(fn () => Auth::user()->id),
                ])->columns(2),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewEmployee::class,
            EditEmployee::class,
            ManageSkill::class,
            ManageResume::class,
        ]);
    }

    public static function getRelations(): array
    {
        $relations = [
            RelationGroup::make('Manage Skills', [
                SkillsRelationManager::class,
            ])
                ->icon('heroicon-o-bolt'),
            RelationGroup::make('Manage Resumes', [
                ResumeRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-document-list'),
        ];

        return $relations;
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'employees/employees';
    }

    public static function getPages(): array
    {
        return [
            'index'   => ListEmployees::route('/'),
            'create'  => CreateEmployee::route('/create'),
            'edit'    => EditEmployee::route('/{record}/edit'),
            'view'    => ViewEmployee::route('/{record}'),
            'skills'  => ManageSkill::route('/{record}/skills'),
            'resumes' => ManageResume::route('/{record}/resumes'),
        ];
    }
}
