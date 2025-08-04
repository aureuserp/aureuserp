<?php

namespace Webkul\Employee\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Enums\DistanceUnit;
use Webkul\Employee\Enums\Gender;
use Webkul\Employee\Enums\MaritalStatus;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategoryResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositionResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages;
use Webkul\Employee\Filament\Resources\EmployeeResource\RelationManagers;
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

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('employees::filament/resources/employee.form.sections.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('job_title')
                                            ->label(__('employees::filament/resources/employee.form.sections.fields.job-title'))
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                    ]),
                                Forms\Components\Group::make()
                                    ->relationship('partner', 'avatar')
                                    ->schema([
                                        Forms\Components\FileUpload::make('avatar')
                                            ->image()
                                            ->hiddenLabel()
                                            ->imageResizeMode('cover')
                                            ->imageEditor()
                                            ->avatar()
                                            ->directory('employees/avatar')
                                            ->visibility('private'),
                                    ]),
                            ])->columns(2),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('work_email')
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
                                Forms\Components\Select::make('department_id')
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.department'))
                                    ->relationship(name: 'department', titleAttribute: 'complete_name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Form $form) => DepartmentResource::form($form)),
                                Forms\Components\TextInput::make('mobile_phone')
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
                                Forms\Components\Select::make('job_id')
                                    ->relationship('job', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.job-position'))
                                    ->createOptionForm(fn (Form $form) => JobPositionResource::form($form)),
                                Forms\Components\TextInput::make('work_phone')
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
                                Forms\Components\Select::make('parent_id')
                                    ->relationship('parent', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->suffixIcon('heroicon-o-user')
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.manager')),
                                Forms\Components\Select::make('employees_employee_categories')
                                    ->multiple()
                                    ->relationship('categories', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.employee-tags'))
                                    ->createOptionForm(fn (Form $form) => EmployeeCategoryResource::form($form)),
                                Forms\Components\Select::make('coach_id')
                                    ->searchable()
                                    ->preload()
                                    ->relationship('coach', 'name')
                                    ->label(__('employees::filament/resources/employee.form.sections.fields.coach')),
                            ])
                            ->columns(2),

                    ])
                    ->columns(1),
                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('employees::filament/resources/employee.form.tabs.work-information.title'))
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.work-information.fields.location'))
                                                    ->schema([
                                                        Forms\Components\Select::make('address_id')
                                                            ->relationship('companyAddress', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->suffixIcon('heroicon-o-map-pin')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.work-address')),
                                                        Forms\Components\Select::make('work_location_id')
                                                            ->relationship('workLocation', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.work-location'))
                                                            ->prefixIcon('heroicon-o-map-pin')
                                                            ->createOptionForm(fn (Form $form) => WorkLocationResource::form($form))
                                                            ->editOptionForm(fn (Form $form) => WorkLocationResource::form($form)),
                                                    ])->columns(1),
                                                Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.work-information.fields.approver'))
                                                    ->schema([
                                                        Forms\Components\Select::make('leave_manager_id')
                                                            ->options(fn () => User::pluck('name', 'id'))
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->suffixIcon('heroicon-o-clock')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.time-off')),
                                                        Forms\Components\Select::make('attendance_manager_id')
                                                            ->options(fn () => User::pluck('name', 'id'))
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->suffixIcon('heroicon-o-clock')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.attendance-manager')),
                                                    ])->columns(1),
                                                Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.work-information.fields.schedule'))
                                                    ->schema([
                                                        Forms\Components\Select::make('calendar_id')
                                                            ->options(fn () => Calendar::pluck('name', 'id'))
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->suffixIcon('heroicon-o-clock')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.working-hours')),
                                                        Forms\Components\Select::make('time_zone')
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
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Group::make()
                                                    ->schema([
                                                        Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.work-information.fields.organization-details'))
                                                            ->schema([
                                                                Forms\Components\Select::make('company_id')
                                                                    ->relationship('company', 'name')
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->prefixIcon('heroicon-o-building-office')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.work-information.fields.company'))
                                                                    ->createOptionForm(fn (Form $form) => CompanyResource::form($form)),
                                                                Forms\Components\ColorPicker::make('color')
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
                        Forms\Components\Tabs\Tab::make(__('employees::filament/resources/employee.form.tabs.private-information.title'))
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Group::make()
                                                    ->schema([
                                                        Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.private-contact'))
                                                            ->schema([
                                                                Forms\Components\TextInput::make('private_street1')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.street-1')),
                                                                Forms\Components\TextInput::make('private_street2')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.street-2')),
                                                                Forms\Components\TextInput::make('private_city')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.city')),
                                                                Forms\Components\TextInput::make('private_zip')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.postal-code')),
                                                                Forms\Components\Select::make('private_country_id')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country'))
                                                                    ->relationship(name: 'country', titleAttribute: 'name')
                                                                    ->afterStateUpdated(fn (Set $set) => $set('private_state_id', null))
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->live(),
                                                                Forms\Components\Select::make('private_state_id')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state'))
                                                                    ->relationship(
                                                                        name: 'state',
                                                                        titleAttribute: 'name',
                                                                        modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query->where('country_id', $get('private_country_id')),
                                                                    )
                                                                    ->createOptionForm(function (Form $form, Forms\Get $get, Forms\Set $set) {
                                                                        return $form
                                                                            ->schema([
                                                                                Forms\Components\TextInput::make('name')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state-name'))
                                                                                    ->required(),
                                                                                Forms\Components\TextInput::make('code')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state-code'))
                                                                                    ->required()
                                                                                    ->unique('states'),
                                                                                Forms\Components\Select::make('country_id')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state-country'))
                                                                                    ->relationship('country', 'name')
                                                                                    ->searchable()
                                                                                    ->preload()
                                                                                    ->live()
                                                                                    ->default($get('country_id'))
                                                                                    ->afterStateUpdated(function (Forms\Get $get) use ($set) {
                                                                                        $set('private_country_id', $get('country_id'));
                                                                                    }),
                                                                            ]);
                                                                    })
                                                                    ->searchable()
                                                                    ->preload(),
                                                                Forms\Components\TextInput::make('private_phone')
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
                                                                Forms\Components\Select::make('bank_account_id')
                                                                    ->relationship('bankAccount', 'account_number')
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->createOptionForm([
                                                                        Forms\Components\Group::make()
                                                                            ->schema([
                                                                                Forms\Components\TextInput::make('account_number')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-account-number'))
                                                                                    ->required(),
                                                                                Forms\Components\Hidden::make('account_holder_name')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-account-holder-name'))
                                                                                    ->default(function (Get $get, $livewire) {
                                                                                        return $livewire->record->user?->name ?? $get('name');
                                                                                    })
                                                                                    ->required(),
                                                                                Forms\Components\Hidden::make('partner_id')
                                                                                    ->default(function (Get $get, $livewire) {
                                                                                        return $livewire->record->partner?->id ?? $get('name');
                                                                                    })
                                                                                    ->required(),
                                                                                Forms\Components\Hidden::make('creator_id')
                                                                                    ->default(fn () => Auth::user()->id),
                                                                                Forms\Components\Select::make('bank_id')
                                                                                    ->relationship('bank', 'name')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank'))
                                                                                    ->searchable()
                                                                                    ->preload()
                                                                                    ->createOptionForm(static::getBankCreateSchema())
                                                                                    ->editOptionForm(static::getBankCreateSchema())
                                                                                    ->createOptionAction(fn (Action $action) => $action->modalHeading(__('employees::filament/resources/employee.form.tabs.private-information.fields.create-bank')))
                                                                                    ->live()
                                                                                    ->required(),
                                                                                Forms\Components\Toggle::make('is_active')
                                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.status'))
                                                                                    ->default(true)
                                                                                    ->inline(false),
                                                                                Forms\Components\Toggle::make('can_send_money')
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
                                                                Forms\Components\TextInput::make('private_email')
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
                                                                Forms\Components\TextInput::make('private_car_plate')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.private-car-plate')),
                                                                Forms\Components\TextInput::make('distance_home_work')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.distance-home-to-work'))
                                                                    ->numeric()
                                                                    ->default(0)
                                                                    ->minValue(0)
                                                                    ->maxValue(99999999999)
                                                                    ->suffix('km'),
                                                                Forms\Components\TextInput::make('km_home_work')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.km-home-to-work'))
                                                                    ->numeric()
                                                                    ->default(0)
                                                                    ->minValue(0)
                                                                    ->maxValue(99999999999)
                                                                    ->suffix('km'),
                                                                Forms\Components\Select::make('distance_home_work_unit')
                                                                    ->options(DistanceUnit::options())
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.distance-unit')),
                                                            ])->columns(2),
                                                        Forms\Components\Group::make()
                                                            ->schema([
                                                                Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.emergency-contact'))
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('emergency_contact')
                                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.contact-name')),
                                                                        Forms\Components\TextInput::make('emergency_phone')
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
                                                        Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.family-status'))
                                                            ->schema([
                                                                Forms\Components\Select::make('marital')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.marital-status'))
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->default(MaritalStatus::Single->value)
                                                                    ->options(MaritalStatus::options())
                                                                    ->live(),
                                                                Forms\Components\TextInput::make('spouse_complete_name')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.spouse-name'))
                                                                    ->hidden(fn (Get $get) => $get('marital') === MaritalStatus::Single->value)
                                                                    ->dehydrated(fn (Get $get) => $get('marital') !== MaritalStatus::Single->value)
                                                                    ->required(fn (Get $get) => $get('marital') !== MaritalStatus::Single->value),
                                                                Forms\Components\DatePicker::make('spouse_birthdate')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.spouse-birthday'))
                                                                    ->native(false)
                                                                    ->suffixIcon('heroicon-o-calendar')
                                                                    ->disabled(fn (Get $get) => $get('marital') === MaritalStatus::Single->value)
                                                                    ->hidden(fn (Get $get) => $get('marital') === MaritalStatus::Single->value)
                                                                    ->dehydrated(fn (Get $get) => $get('marital') !== MaritalStatus::Single->value),
                                                                Forms\Components\TextInput::make('children')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.number-of-children'))
                                                                    ->numeric()
                                                                    ->minValue(0)
                                                                    ->maxValue(99999999999)
                                                                    ->disabled(fn (Get $get) => $get('marital') === MaritalStatus::Single->value)
                                                                    ->hidden(fn (Get $get) => $get('marital') === MaritalStatus::Single->value)
                                                                    ->dehydrated(fn (Get $get) => $get('marital') !== MaritalStatus::Single->value),
                                                            ])->columns(2),
                                                        Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.education'))
                                                            ->schema([
                                                                Forms\Components\Select::make('certificate')
                                                                    ->options([
                                                                        'graduate' => __('employees::filament/resources/employee.form.tabs.private-information.fields.graduated'),
                                                                        'bachelor' => __('employees::filament/resources/employee.form.tabs.private-information.fields.bachelor'),
                                                                        'master'   => __('employees::filament/resources/employee.form.tabs.private-information.fields.master'),
                                                                        'doctor'   => __('employees::filament/resources/employee.form.tabs.private-information.fields.doctor'),
                                                                        'other'    => __('employees::filament/resources/employee.form.tabs.private-information.fields.other'),
                                                                    ])
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.certificate-level')),
                                                                Forms\Components\TextInput::make('study_field')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.field-of-study')),
                                                                Forms\Components\TextInput::make('study_school')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.school')),
                                                            ])->columns(1),

                                                    ]),
                                            ])
                                            ->columnSpan(['lg' => 2]),
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.citizenship'))
                                                    ->schema([
                                                        Forms\Components\Select::make('country_id')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country'))
                                                            ->relationship(name: 'country', titleAttribute: 'name')
                                                            ->createOptionForm([
                                                                Forms\Components\TextInput::make('name')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country-name'))
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('code')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country-code'))
                                                                    ->required()
                                                                    ->rules('max:2'),
                                                                Forms\Components\Toggle::make('state_required')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country-state-required'))
                                                                    ->required(),
                                                                Forms\Components\Toggle::make('zip_required')
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
                                                        Forms\Components\Select::make('state_id')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state'))
                                                            ->relationship(
                                                                name: 'state',
                                                                titleAttribute: 'name',
                                                                modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                                                            )
                                                            ->createOptionForm([
                                                                Forms\Components\TextInput::make('name')
                                                                    ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.state-name'))
                                                                    ->required()
                                                                    ->maxLength(255),
                                                                Forms\Components\TextInput::make('code')
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
                                                        Forms\Components\TextInput::make('identification_id')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.identification-id')),
                                                        Forms\Components\TextInput::make('ssnid')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.ssnid')),
                                                        Forms\Components\TextInput::make('sinid')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.sinid')),
                                                        Forms\Components\TextInput::make('passport_id')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.passport-id')),
                                                        Forms\Components\Select::make('gender')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.gender'))
                                                            ->searchable()
                                                            ->preload()
                                                            ->options(Gender::options()),
                                                        Forms\Components\DatePicker::make('birthday')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.date-of-birth'))
                                                            ->suffixIcon('heroicon-o-calendar')
                                                            ->native(false)
                                                            ->maxDate(now()),
                                                        Forms\Components\Select::make('country_of_birth')
                                                            ->relationship('countryOfBirth', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.country-of-birth')),

                                                    ])->columns(1),
                                                Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.private-information.fields.work-permit'))
                                                    ->schema([
                                                        Forms\Components\TextInput::make('visa_no')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.visa-number')),
                                                        Forms\Components\TextInput::make('permit_no')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.work-permit-no')),
                                                        Forms\Components\DatePicker::make('visa_expire')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.visa-expiration-date'))
                                                            ->suffixIcon('heroicon-o-calendar')
                                                            ->native(false),
                                                        Forms\Components\DatePicker::make('work_permit_expiration_date')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.work-permit-expiration-date'))
                                                            ->suffixIcon('heroicon-o-calendar')
                                                            ->native(false),
                                                        Forms\Components\FileUpload::make('work_permit')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.work-permit'))
                                                            ->panelAspectRatio('4:1')
                                                            ->panelLayout('integrated')
                                                            ->directory('employees/work-permit')
                                                            ->visibility('private'),
                                                    ])->columns(1),
                                            ])
                                            ->columnSpan(['lg' => 1]),
                                    ])
                                    ->columns(3),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('employees::filament/resources/employee.form.tabs.settings.title'))
                            ->icon('heroicon-o-cog-8-tooth')
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.settings.fields.employment-status'))
                                                    ->schema([
                                                        Forms\Components\Toggle::make('is_active')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.active-employee'))
                                                            ->default(true)
                                                            ->inline(false),
                                                        Forms\Components\Toggle::make('is_flexible')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.flexible-work-arrangement'))
                                                            ->inline(false),
                                                        Forms\Components\Toggle::make('is_fully_flexible')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.fully-flexible-schedule'))
                                                            ->inline(false),
                                                        Forms\Components\Toggle::make('work_permit_scheduled_activity')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.work-permit-scheduled-activity')),
                                                        Forms\Components\Select::make('user_id')
                                                            ->relationship(name: 'user', titleAttribute: 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.related-user'))
                                                            ->prefixIcon('heroicon-o-user')
                                                            ->createOptionForm(fn (Form $form) => UserResource::form($form))
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
                                                        Forms\Components\Select::make('departure_reason_id')
                                                            ->relationship('departureReason', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.departure-reason'))
                                                            ->createOptionForm(fn (Form $form) => DepartureReasonResource::form($form)),
                                                        Forms\Components\DatePicker::make('departure_date')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.departure-date'))
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) => $get('departure_reason_id') === null)
                                                            ->disabled(fn (Get $get) => $get('departure_reason_id') === null)
                                                            ->required(fn (Get $get) => $get('departure_reason_id') !== null),
                                                        Forms\Components\Textarea::make('departure_description')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.departure-description'))
                                                            ->hidden(fn (Get $get) => $get('departure_reason_id') === null)
                                                            ->disabled(fn (Get $get) => $get('departure_reason_id') === null)
                                                            ->required(fn (Get $get) => $get('departure_reason_id') !== null),
                                                    ])->columns(2),
                                                Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.settings.fields.additional-information'))
                                                    ->schema([
                                                        Forms\Components\TextInput::make('lang')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.primary-language')),
                                                        Forms\Components\Textarea::make('additional_note')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.additional-notes'))
                                                            ->rows(3),
                                                        Forms\Components\Textarea::make('notes')
                                                            ->label(__('employees::filament/resources/employee.form.tabs.settings.fields.notes')),
                                                        ...static::getCustomFormFields(),
                                                    ])->columns(2),
                                            ])
                                            ->columnSpan(['lg' => 2]),
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Fieldset::make(__('employees::filament/resources/employee.form.tabs.settings.fields.attendance-point-of-sale'))
                                                    ->schema([
                                                        Forms\Components\TextInput::make('barcode')
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
                                                        Forms\Components\TextInput::make('pin')
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
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('partner.avatar')
                        ->height(150)
                        ->width(200),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->label(__('employees::filament/resources/employee.table.columns.name'))
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('job_title')
                                ->icon('heroicon-m-briefcase')
                                ->searchable()
                                ->sortable()
                                ->label(__('employees::filament/resources/employee.table.columns.job-title')),
                        ])
                            ->visible(fn ($record) => filled($record->job_title)),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('work_email')
                                ->icon('heroicon-o-envelope')
                                ->searchable()
                                ->sortable()
                                ->label(__('employees::filament/resources/employee.table.columns.work-email'))
                                ->color('gray')
                                ->limit(20),
                        ])
                            ->visible(fn ($record) => filled($record->work_email)),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('work_phone')
                                ->icon('heroicon-o-phone')
                                ->searchable()
                                ->label(__('employees::filament/resources/employee.table.columns.work-phone'))
                                ->color('gray')
                                ->limit(30)
                                ->sortable(),
                        ])
                            ->visible(fn ($record) => filled($record->work_phone)),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('categories.name')
                                ->label(__('employees::filament/resources/employee.table.columns.categories'))
                                ->badge()
                                ->state(function (Employee $record): array {
                                    return $record->categories->map(fn ($category) => [
                                        'label' => $category->name,
                                        'color' => $category->color ?? '#808080',
                                    ])->toArray();
                                })
                                ->formatStateUsing(fn ($state) => $state['label'])
                                ->color(fn ($state) => Color::hex($state['color']))
                                ->weight(FontWeight::Bold),
                        ])
                            ->visible(fn ($record): bool => (bool) $record->categories()->get()?->count()),
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
                Tables\Filters\SelectFilter::make('skills')
                    ->relationship('skills.skill', 'name')
                    ->searchable()
                    ->multiple()
                    ->label(__('employees::filament/resources/employee.table.filters.skills'))
                    ->preload(),
                Tables\Filters\SelectFilter::make('resumes')
                    ->relationship('resumes', 'name')
                    ->searchable()
                    ->label(__('employees::filament/resources/employee.table.filters.resumes'))
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('time_zone')
                    ->options(function () {
                        return collect(timezone_identifiers_list())->mapWithKeys(function ($timezone) {
                            return [$timezone => $timezone];
                        });
                    })
                    ->searchable()
                    ->label(__('employees::filament/resources/employee.table.filters.timezone'))
                    ->multiple()
                    ->preload(),
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(5)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('job_title')
                            ->label(__('employees::filament/resources/employee.table.filters.job-title'))
                            ->icon('heroicon-o-user-circle'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('birthday')
                            ->label(__('employees::filament/resources/employee.table.filters.birthdate'))
                            ->icon('heroicon-o-cake'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('work_email')
                            ->label(__('employees::filament/resources/employee.table.filters.work-email'))
                            ->icon('heroicon-o-at-symbol'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('mobile_phone')
                            ->label(__('employees::filament/resources/employee.table.filters.mobile-phone'))
                            ->icon('heroicon-o-phone'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('work_phone')
                            ->label(__('employees::filament/resources/employee.table.filters.work-phone'))
                            ->icon('heroicon-o-phone'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('is_flexible')
                            ->label(__('employees::filament/resources/employee.table.filters.is-flexible'))
                            ->icon('heroicon-o-cube'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('is_fully_flexible')
                            ->label(__('employees::filament/resources/employee.table.filters.is-fully-flexible'))
                            ->icon('heroicon-o-cube'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('is_active')
                            ->label(__('employees::filament/resources/employee.table.filters.is-active'))
                            ->icon('heroicon-o-cube'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('work_permit_scheduled_activity')
                            ->label(__('employees::filament/resources/employee.table.filters.work-permit-scheduled-activity'))
                            ->icon('heroicon-o-cube'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('emergency_contact')
                            ->label(__('employees::filament/resources/employee.table.filters.emergency-contact'))
                            ->icon('heroicon-o-phone'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('emergency_phone')
                            ->label(__('employees::filament/resources/employee.table.filters.emergency-phone'))
                            ->icon('heroicon-o-phone'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('private_phone')
                            ->label(__('employees::filament/resources/employee.table.filters.private-phone'))
                            ->icon('heroicon-o-phone'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('private_email')
                            ->label(__('employees::filament/resources/employee.table.filters.private-email'))
                            ->icon('heroicon-o-at-symbol'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('private_car_plate')
                            ->label(__('employees::filament/resources/employee.table.filters.private-car-plate'))
                            ->icon('heroicon-o-clipboard-document'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('distance_home_work')
                            ->label(__('employees::filament/resources/employee.table.filters.distance-home-work'))
                            ->icon('heroicon-o-map'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('km_home_work')
                            ->label(__('employees::filament/resources/employee.table.filters.km-home-work'))
                            ->icon('heroicon-o-map'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('distance_home_work_unit')
                            ->label(__('employees::filament/resources/employee.table.filters.distance-home-work-unit'))
                            ->icon('heroicon-o-map'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('marital')
                            ->label(__('employees::filament/resources/employee.table.filters.marital-status'))
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('spouse_complete_name')
                            ->label(__('employees::filament/resources/employee.table.filters.spouse-name'))
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('spouse_birthdate')
                            ->label(__('employees::filament/resources/employee.table.filters.spouse-birthdate'))
                            ->icon('heroicon-o-cake'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('certificate')
                            ->label(__('employees::filament/resources/employee.table.filters.certificate'))
                            ->icon('heroicon-o-document'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('study_field')
                            ->label(__('employees::filament/resources/employee.table.filters.study-field'))
                            ->icon('heroicon-o-academic-cap'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('study_school')
                            ->label(__('employees::filament/resources/employee.table.filters.study-school'))
                            ->icon('heroicon-o-academic-cap'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('identification_id')
                            ->label(__('employees::filament/resources/employee.table.filters.identification-id'))
                            ->icon('heroicon-o-credit-card'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('ssnid')
                            ->label(__('employees::filament/resources/employee.table.filters.ssnid'))
                            ->icon('heroicon-o-credit-card'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('sinid')
                            ->label(__('employees::filament/resources/employee.table.filters.sinid'))
                            ->icon('heroicon-o-credit-card'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('passport_id')
                            ->label(__('employees::filament/resources/employee.table.filters.passport-id'))
                            ->icon('heroicon-o-credit-card'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('gender')
                            ->label(__('employees::filament/resources/employee.table.filters.gender'))
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\NumberConstraint::make('children')
                            ->label(__('employees::filament/resources/employee.table.filters.children'))
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('visa_no')
                            ->label(__('employees::filament/resources/employee.table.filters.visa-no'))
                            ->icon('heroicon-o-credit-card'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('permit_no')
                            ->label(__('employees::filament/resources/employee.table.filters.permit-no'))
                            ->icon('heroicon-o-credit-card'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('lang')
                            ->label(__('employees::filament/resources/employee.table.filters.language'))
                            ->icon('heroicon-o-language'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('additional_note')
                            ->label(__('employees::filament/resources/employee.table.filters.additional-note'))
                            ->icon('heroicon-o-language'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('notes')
                            ->label(__('employees::filament/resources/employee.table.filters.notes'))
                            ->icon('heroicon-o-language'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('barcode')
                            ->label(__('employees::filament/resources/employee.table.filters.barcode'))
                            ->icon('heroicon-o-qr-code'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('visa_expire')
                            ->label(__('employees::filament/resources/employee.table.filters.visa-expire'))
                            ->icon('heroicon-o-credit-card'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('work_permit_expiration_date')
                            ->label(__('employees::filament/resources/employee.table.filters.work-permit-expiration-date'))
                            ->icon('heroicon-o-calendar'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('departure_date')
                            ->label(__('employees::filament/resources/employee.table.filters.departure-date'))
                            ->icon('heroicon-o-calendar'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('departure_description')
                            ->label(__('employees::filament/resources/employee.table.filters.departure-description'))
                            ->icon('heroicon-o-cube'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('employees::filament/resources/employee.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('employees::filament/resources/employee.table.filters.updated-at')),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('creator')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('calendar')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('department')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('job')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('partner')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('leaveManager')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('attendanceManager')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('workLocation')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('parent')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('coach')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('privateState')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('privateCountry')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('country')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('state')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('countryOfBirth')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('bankAccount')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('departureReason')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('employmentType')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('categories')
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
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->outlined(),
                Tables\Actions\EditAction::make()
                    ->outlined(),
                Tables\Actions\RestoreAction::make()
                    ->outlined()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee.table.actions.restore.notification.title'))
                            ->body(__('employees::filament/resources/employee.table.actions.restore.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/resources/employee.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/employee.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/resources/employee.table.bulk-actions.delete.notification.body'))
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/employee.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/resources/employee.table.bulk-actions.force-delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Grid::make(['default' => 2])
                            ->schema([
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('name')
                                        ->label(__('employees::filament/resources/employee.infolist.sections.entries.name'))
                                        ->weight(FontWeight::Bold)
                                        ->placeholder('—')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                    Infolists\Components\TextEntry::make('job_title')
                                        ->placeholder('—')
                                        ->label(__('employees::filament/resources/employee.infolist.sections.entries.job-title')),
                                ])->columnSpan(1),
                                Infolists\Components\Group::make([
                                    Infolists\Components\ImageEntry::make('partner.avatar')
                                        ->hiddenLabel()
                                        ->height(140)
                                        ->circular(),
                                ])->columnSpan(1),
                            ]),
                        Infolists\Components\Grid::make(['default' => 2])
                            ->schema([
                                Infolists\Components\TextEntry::make('work_email')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.work-email'))
                                    ->placeholder('—')
                                    ->url(fn (?string $state) => $state ? "mailto:{$state}" : '#')
                                    ->icon('heroicon-o-envelope')
                                    ->iconPosition(IconPosition::Before),
                                Infolists\Components\TextEntry::make('department.complete_name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.department')),
                                Infolists\Components\TextEntry::make('mobile_phone')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.work-mobile'))
                                    ->placeholder('—')
                                    ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                    ->icon('heroicon-o-phone')
                                    ->iconPosition(IconPosition::Before),
                                Infolists\Components\TextEntry::make('job.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.job-position')),
                                Infolists\Components\TextEntry::make('work_phone')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.work-phone'))
                                    ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                    ->icon('heroicon-o-phone')
                                    ->iconPosition(IconPosition::Before),
                                Infolists\Components\TextEntry::make('parent.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.manager')),
                                Infolists\Components\TextEntry::make('categories.name')
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
                                    ->color(fn ($state) => Color::hex($state['color']))
                                    ->listWithLineBreaks(),
                                Infolists\Components\TextEntry::make('coach.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.coach')),
                            ]),
                    ]),

                Tabs::make()
                    ->tabs([
                        Tabs\Tab::make(__('employees::filament/resources/employee.infolist.tabs.work-information.title'))
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Infolists\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Infolists\Components\Group::make([
                                            Infolists\Components\Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.location'))
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('companyAddress.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.work-address'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    Infolists\Components\TextEntry::make('workLocation.name')
                                                        ->placeholder('—')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.work-location'))
                                                        ->icon('heroicon-o-building-office'),
                                                ]),
                                            Infolists\Components\Fieldset::make('Approvers')
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('leaveManager.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.time-off'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-user-group'),
                                                    Infolists\Components\TextEntry::make('attendanceManager.name')
                                                        ->placeholder('—')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.attendance-manager'))
                                                        ->icon('heroicon-o-user-group'),
                                                ]),
                                            Infolists\Components\Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.schedule'))
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('calendar.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.working-hours'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-clock'),
                                                    Infolists\Components\TextEntry::make('time_zone')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.timezone'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-clock'),
                                                ]),
                                        ])->columnSpan(2),
                                        Infolists\Components\Group::make([
                                            Infolists\Components\Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.organization-details'))
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('company.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.company'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-briefcase'),
                                                    Infolists\Components\ColorEntry::make('color')
                                                        ->placeholder('—')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.color')),
                                                ]),
                                        ])->columnSpan(1),
                                    ]),
                            ]),
                        Tabs\Tab::make(__('employees::filament/resources/employee.infolist.tabs.private-information.title'))
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Infolists\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Infolists\Components\Group::make([
                                            Infolists\Components\Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-contact'))
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('private_street1')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.street-address'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    Infolists\Components\TextEntry::make('private_street2')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.street-address-line-2'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    Infolists\Components\TextEntry::make('private_city')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.city'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-building-office'),
                                                    Infolists\Components\TextEntry::make('private_zip')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.post-code'))
                                                        ->icon('heroicon-o-document-text'),
                                                    Infolists\Components\TextEntry::make('privateCountry.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.country'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-globe-alt'),
                                                    Infolists\Components\TextEntry::make('privateState.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.state'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    Infolists\Components\TextEntry::make('private_phone')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-phone'))
                                                        ->placeholder('—')
                                                        ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                                        ->icon('heroicon-o-phone'),
                                                    Infolists\Components\TextEntry::make('private_email')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-email'))
                                                        ->placeholder('—')
                                                        ->url(fn (?string $state) => $state ? "mailto:{$state}" : '#')
                                                        ->icon('heroicon-o-envelope'),
                                                    Infolists\Components\TextEntry::make('private_car_plate')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-car-plate'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-rectangle-stack'),
                                                    Infolists\Components\TextEntry::make('distance_home_work')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.distance-home-to-work'))
                                                        ->placeholder('—')
                                                        ->suffix('km')
                                                        ->icon('heroicon-o-map'),
                                                ]),
                                            Infolists\Components\Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.emergency-contact'))
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('emergency_contact')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.contact-name'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-user'),
                                                    Infolists\Components\TextEntry::make('emergency_phone')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.contact-phone'))
                                                        ->placeholder('—')
                                                        ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                                        ->icon('heroicon-o-phone'),
                                                ]),
                                            Infolists\Components\Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit'))
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('visa_no')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.visa-number'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-document-text')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.visa-number-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    Infolists\Components\TextEntry::make('permit_no')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-number'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-rectangle-stack')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-number-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    Infolists\Components\TextEntry::make('visa_expire')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.visa-expiration-date'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-calendar-days')
                                                        ->date('F j, Y')
                                                        ->color(
                                                            fn ($record) => $record->visa_expire && now()->diffInDays($record->visa_expire, false) <= 30
                                                                ? 'danger'
                                                                : 'success'
                                                        ),
                                                    Infolists\Components\TextEntry::make('work_permit_expiration_date')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-expiration-date'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-calendar-days')
                                                        ->date('F j, Y')
                                                        ->color(
                                                            fn ($record) => $record->work_permit_expiration_date && now()->diffInDays($record->work_permit_expiration_date, false) <= 30
                                                                ? 'danger'
                                                                : 'success'
                                                        ),
                                                    Infolists\Components\ImageEntry::make('work_permit')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-document'))
                                                        ->columnSpanFull()
                                                        ->placeholder('—')
                                                        ->height(200),
                                                ]),
                                        ])->columnSpan(2),
                                        Infolists\Components\Group::make([
                                            Infolists\Components\Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.citizenship'))
                                                ->columns(1)
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('country.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.country'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-globe-alt'),
                                                    Infolists\Components\TextEntry::make('state.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.state'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    Infolists\Components\TextEntry::make('identification_id')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.identification-id'))
                                                        ->icon('heroicon-o-document-text')
                                                        ->placeholder('—')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.identification-id-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    Infolists\Components\TextEntry::make('ssnid')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.ssnid'))
                                                        ->icon('heroicon-o-document-check')
                                                        ->placeholder('—')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.ssnid-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    Infolists\Components\TextEntry::make('sinid')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.sinid'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-document')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.sinid-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    Infolists\Components\TextEntry::make('passport_id')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.passport-id'))
                                                        ->icon('heroicon-o-identification')
                                                        ->copyable()
                                                        ->placeholder('—')
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.passport-id-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    Infolists\Components\TextEntry::make('gender')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.gender'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-user')
                                                        ->badge()
                                                        ->color(fn (string $state): string => match ($state) {
                                                            'male'   => 'info',
                                                            'female' => 'success',
                                                            default  => 'warning',
                                                        }),
                                                    Infolists\Components\TextEntry::make('birthday')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.date-of-birth'))
                                                        ->icon('heroicon-o-calendar')
                                                        ->placeholder('—')
                                                        ->date('F j, Y'),
                                                    Infolists\Components\TextEntry::make('countryOfBirth.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.country-of-birth'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-globe-alt'),
                                                    Infolists\Components\TextEntry::make('country.phone_code')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.phone-code'))
                                                        ->icon('heroicon-o-phone')
                                                        ->placeholder('—')
                                                        ->prefix('+'),
                                                ]),
                                        ])->columnSpan(1),
                                    ]),
                            ]),
                        Tabs\Tab::make(__('employees::filament/resources/employee.infolist.tabs.settings.title'))
                            ->icon('heroicon-o-cog-8-tooth')
                            ->schema([
                                Infolists\Components\Group::make()
                                    ->schema([
                                        Infolists\Components\Group::make()
                                            ->schema([
                                                Infolists\Components\Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.settings.entries.employee-settings'))
                                                    ->schema([
                                                        Infolists\Components\IconEntry::make('is_active')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.active-employee'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        Infolists\Components\IconEntry::make('is_flexible')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.flexible-work-arrangement'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        Infolists\Components\IconEntry::make('is_fully_flexible')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.fully-flexible-schedule'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        Infolists\Components\IconEntry::make('work_permit_scheduled_activity')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.work-permit-scheduled-activity'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        Infolists\Components\TextEntry::make('user.name')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.related-user'))
                                                            ->placeholder('—')
                                                            ->icon('heroicon-o-user'),
                                                        Infolists\Components\TextEntry::make('departureReason.name')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.departure-reason')),
                                                        Infolists\Components\TextEntry::make('departure_date')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.departure-date'))
                                                            ->icon('heroicon-o-calendar-days'),
                                                        Infolists\Components\TextEntry::make('departure_description')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.departure-description')),
                                                    ])
                                                    ->columns(2),
                                                Infolists\Components\Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.settings.entries.additional-information'))
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('lang')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.primary-language')),
                                                        Infolists\Components\TextEntry::make('additional_note')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.additional-notes'))
                                                            ->columnSpanFull(),
                                                        Infolists\Components\TextEntry::make('notes')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.notes')),
                                                    ])
                                                    ->columns(2),
                                            ])
                                            ->columnSpan(['lg' => 2]),
                                        Infolists\Components\Group::make()
                                            ->schema([
                                                Infolists\Components\Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.settings.entries.attendance-point-of-sale'))
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('barcode')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.badge-id'))
                                                            ->icon('heroicon-o-qr-code'),
                                                        Infolists\Components\TextEntry::make('pin')
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
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-name'))
                        ->required(),
                    Forms\Components\TextInput::make('code')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-code'))
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-email'))
                        ->email()
                        ->required(),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-phone-number'))
                        ->tel(),
                    Forms\Components\TextInput::make('street1')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-street-1')),
                    Forms\Components\TextInput::make('street2')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-street-2')),
                    Forms\Components\TextInput::make('city')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-city')),
                    Forms\Components\TextInput::make('zip')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-zipcode')),
                    Forms\Components\Select::make('country_id')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-country'))
                        ->relationship(name: 'country', titleAttribute: 'name')
                        ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                        ->searchable()
                        ->preload()
                        ->live(),
                    Forms\Components\Select::make('state_id')
                        ->label(__('employees::filament/resources/employee.form.tabs.private-information.fields.bank-state'))
                        ->relationship(
                            name: 'state',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                        )
                        ->searchable()
                        ->preload()
                        ->required(fn (Get $get) => Country::find($get('country_id'))?->state_required),
                    Forms\Components\Hidden::make('creator_id')
                        ->default(fn () => Auth::user()->id),
                ])->columns(2),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewEmployee::class,
            Pages\EditEmployee::class,
            Pages\ManageSkill::class,
            Pages\ManageResume::class,
        ]);
    }

    public static function getRelations(): array
    {
        $relations = [
            RelationGroup::make('Manage Skills', [
                RelationManagers\SkillsRelationManager::class,
            ])
                ->icon('heroicon-o-bolt'),
            RelationGroup::make('Manage Resumes', [
                RelationManagers\ResumeRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-document-list'),
        ];

        return $relations;
    }

    public static function getSlug(): string
    {
        return 'employees/employees';
    }

    public static function getPages(): array
    {
        return [
            'index'   => Pages\ListEmployees::route('/'),
            'create'  => Pages\CreateEmployee::route('/create'),
            'edit'    => Pages\EditEmployee::route('/{record}/edit'),
            'view'    => Pages\ViewEmployee::route('/{record}'),
            'skills'  => Pages\ManageSkill::route('/{record}/skills'),
            'resumes' => Pages\ManageResume::route('/{record}/resumes'),
        ];
    }
}
