<?php

namespace Webkul\Employee\Filament\Resources\Employees;

use BackedEnum;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Filament\Resources\Employees\Pages\CreateEmployee;
use Webkul\Employee\Filament\Resources\Employees\Pages\EditEmployee;
use Webkul\Employee\Filament\Resources\Employees\Pages\ListEmployees;
use Webkul\Employee\Filament\Resources\Employees\Pages\ManageResume;
use Webkul\Employee\Filament\Resources\Employees\Pages\ManageSkill;
use Webkul\Employee\Filament\Resources\Employees\Pages\ViewEmployee;
use Webkul\Employee\Filament\Resources\Employees\RelationManagers\ResumeRelationManager;
use Webkul\Employee\Filament\Resources\Employees\RelationManagers\SkillsRelationManager;
use Webkul\Employee\Filament\Resources\Employees\Schemas\EmployeeForm;
use Webkul\Employee\Filament\Resources\Employees\Schemas\EmployeeInfolist;
use Webkul\Employee\Filament\Resources\Employees\Tables\EmployeesTable;
use Webkul\Employee\Models\Employee;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Support\Models\Country;

class EmployeeResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

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
        return EmployeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmployeeInfolist::configure($schema);
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
