<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Unique;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\CreateTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\EditTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ListTeams;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ViewTeam;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityForm;
use Webkul\Crm\Filament\Concerns\HasCrmAuthorization;
use Webkul\Crm\Filament\Support\AssignmentRuleFields;
use Webkul\Crm\Models\CrmTeam;
use Webkul\Crm\Models\MailAlias;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Policies\TeamPolicy;
use Webkul\Crm\Settings\CrmSettings;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource as BaseTeamResource;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource as BaseUserResource;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

class TeamResource extends BaseTeamResource
{
    use HasCrmAuthorization;

    protected static ?string $cluster = Configurations::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.navigation.title');
    }

    public static function getCrmPolicy(): string
    {
        return TeamPolicy::class;
    }

    public static function form(Schema $schema): Schema
    {
        $schema = parent::form($schema);

        $label = fn (string $key): string => __('crm::filament/assignment.team.'.$key);

        $salesComponents = $schema->getComponents(withHidden: true);

        static::findSalesMembersSelect($salesComponents)?->hidden();

        return $schema->components([
            ...$salesComponents,
            Section::make($label('section'))
                ->description($label('section-description'))
                ->relationship('crmTeam')
                ->visible(fn (): bool => static::usesRuleBasedAssignment())
                ->schema([
                    Text::make(fn (?CrmTeam $record): string => static::getCapacitySummary((int) $record?->team_id))
                        ->visible(fn (?CrmTeam $record): bool => filled($record?->team_id))
                        ->columnSpanFull(),
                    AssignmentRuleFields::make(framed: false)
                        ->columnSpanFull(),
                ]),
            static::getAliasSection(),
            static::getMembersSection(),
        ]);
    }

    protected static function findSalesMembersSelect(array $components): ?Select
    {
        foreach ($components as $component) {
            if ($component instanceof Select && $component->getName() === 'sales_team_members') {
                return $component;
            }

            if (! $component instanceof Component) {
                continue;
            }

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                if ($found = static::findSalesMembersSelect($childSchema->getComponents(withHidden: true))) {
                    return $found;
                }
            }
        }

        return null;
    }

    public static function getAliasSection(): Section
    {
        $label = fn (string $key, array $replace = []): string => __('crm::filament/mail.alias.'.$key, $replace);

        return Section::make($label('section'))
            ->description($label('section-description'))
            ->relationship('crmAlias')
            ->columns(3)
            ->schema([
                TextInput::make('alias_name')
                    ->label($label('name'))
                    ->helperText($label('name-helper'))
                    ->regex('/^[a-zA-Z0-9]+([._-][a-zA-Z0-9]+)*$/')
                    ->validationMessages(['regex' => $label('name-invalid'), 'unique' => $label('name-taken')])
                    ->unique(
                        MailAlias::class,
                        'alias_name',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get): Unique => filled($domain = MailAlias::normalizeDomain($get('alias_domain')))
                            ? $rule->where('alias_domain', $domain)
                            : $rule->whereNull('alias_domain'),
                    )
                    ->dehydrateStateUsing(fn (?string $state): ?string => MailAlias::normalizeName($state))
                    ->suffix('@')
                    ->maxLength(100),
                TextInput::make('alias_domain')
                    ->label($label('domain'))
                    ->helperText($label('domain-helper'))
                    ->placeholder('yourcompany.com')
                    ->regex('/^@?(?!-)[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)+$/')
                    ->validationMessages(['regex' => $label('domain-invalid')])
                    ->requiredWith('alias_name')
                    ->dehydrateStateUsing(fn (?string $state): ?string => MailAlias::normalizeDomain($state))
                    ->maxLength(255),
                Select::make('alias_contact')
                    ->label($label('contact'))
                    ->options(collect(MailAlias::CONTACTS)->mapWithKeys(fn (string $contact): array => [$contact => __('crm::filament/mail.alias-contacts.'.$contact)])->all())
                    ->helperText(fn (Get $get): string => __('crm::filament/mail.alias-contacts-help.'.($get('alias_contact') ?: MailAlias::CONTACT_EVERYONE)))
                    ->default(MailAlias::CONTACT_EVERYONE)
                    ->selectablePlaceholder(false)
                    ->live()
                    ->required(),
            ]);
    }

    public static function getNewSalespersonFields(): array
    {
        $label = fn (string $key): string => __('security::filament/resources/user.form.sections.'.$key);

        return [
            TextInput::make('name')
                ->label($label('general-information.fields.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label($label('general-information.fields.email'))
                ->email()
                ->required()
                ->unique(User::class, 'email')
                ->maxLength(255),
            TextInput::make('password')
                ->label($label('general-information.fields.password'))
                ->password()
                ->revealable()
                ->required()
                ->rule('min:8')
                ->maxLength(255),
            TextInput::make('password_confirmation')
                ->label($label('general-information.fields.password-confirmation'))
                ->password()
                ->revealable()
                ->required()
                ->same('password'),
            Select::make('roles')
                ->label($label('permissions.fields.roles'))
                ->options(fn (): array => Role::query()->orderBy('name')->pluck('name', 'id')->all())
                ->multiple()
                ->required()
                ->searchable()
                ->columnSpanFull(),
        ];
    }

    public static function createSalesperson(array $data, mixed $companyId): int
    {
        abort_unless(UserResource::canCreate(), 403);

        $roleIds = Role::query()->whereKey((array) ($data['roles'] ?? []))->pluck('id')->all();

        BaseUserResource::ensureAdminRoleConstraints(null, $roleIds);

        $companyId = filled($companyId) ? (int) $companyId : current_company_id();

        abort_unless($companyId && static::canUseCompany((int) $companyId), 403);

        return DB::transaction(function () use ($data, $companyId, $roleIds): int {
            $user = User::query()->create([
                'name'                => $data['name'],
                'email'               => $data['email'],
                'password'            => Hash::make($data['password']),
                'is_active'           => true,
                'resource_permission' => PermissionType::GLOBAL,
                'default_company_id'  => $companyId,
                'creator_id'          => Auth::id(),
            ]);

            if ($companyId) {
                $user->allowedCompanies()->syncWithoutDetaching([$companyId]);
            }

            $user->roles()->sync($roleIds);

            return $user->getKey();
        });
    }

    protected static function canUseCompany(int $companyId): bool
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return false;
        }

        return (int) $user->default_company_id === $companyId
            || $user->allowedCompanies()->whereKey($companyId)->exists();
    }

    public static function getMembersSection(): Section
    {
        $label = fn (string $key): string => __('crm::filament/assignment.members.'.$key);

        return Section::make($label('title'))
            ->description($label('description'))
            ->schema([
                Repeater::make('crmMembers')
                    ->relationship('crmMembers')
                    ->hiddenLabel()
                    ->schema([
                        Select::make('user_id')
                            ->label($label('fields.salesperson'))
                            ->options(fn (Get $get): array => OpportunityForm::scopeSalespeople(User::query(), $get('../../company_id'))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->live()
                            ->createOptionForm(fn (): array => static::getNewSalespersonFields())
                            ->createOptionModalHeading($label('new-salesperson.heading'))
                            ->createOptionAction(fn (Action $action): Action => $action
                                ->tooltip($label('new-salesperson.heading'))
                                ->visible(fn (): bool => UserResource::canCreate())
                                ->authorize(fn (): bool => UserResource::canCreate()))
                            ->createOptionUsing(fn (array $data, Get $get): int => static::createSalesperson($data, $get('../../company_id'))),
                        TextInput::make('assignment_max')
                            ->visible(fn (): bool => static::usesRuleBasedAssignment())
                            ->label($label('fields.capacity'))
                            ->helperText(fn (?TeamMember $record): string => $record
                                ? __('crm::filament/assignment.members.fields.capacity-assigned', ['count' => $record->leadsAssignedLastMonth()])
                                : $label('fields.capacity-helper'))
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(30)
                            ->required(),
                        Toggle::make('assignment_optout')
                            ->visible(fn (): bool => static::usesRuleBasedAssignment())
                            ->label($label('fields.paused'))
                            ->helperText($label('fields.paused-helper'))
                            ->inline(false),
                        Toggle::make('is_active')
                            ->label($label('fields.active'))
                            ->default(true)
                            ->inline(false),
                        AssignmentRuleFields::make()
                            ->visible(fn (): bool => static::usesRuleBasedAssignment())
                            ->columnSpanFull(),
                    ])
                    ->columns(4)
                    ->itemLabel(fn (array $state): ?string => filled($state['user_id'] ?? null) ? User::query()->whereKey($state['user_id'])->value('name') : null)
                    ->collapsible()
                    ->collapsed(fn (string $operation): bool => $operation !== 'create')
                    ->defaultItems(0)
                    ->reorderable(false)
                    ->addActionLabel($label('actions.add')),
            ]);
    }

    public static function usesRuleBasedAssignment(): bool
    {
        return (bool) app(CrmSettings::class)->use_auto_assignment;
    }

    public static function getCapacitySummary(int $teamId): string
    {
        $members = TeamMember::query()
            ->where('team_id', $teamId)
            ->where('is_active', true)
            ->where('assignment_optout', false)
            ->get();

        return __('crm::filament/assignment.team.capacity', [
            'assigned' => $members->sum(fn (TeamMember $member): int => $member->leadsAssignedLastMonth()),
            'max'      => $members->sum('assignment_max'),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'view'   => ViewTeam::route('/{record}'),
            'edit'   => EditTeam::route('/{record}/edit'),
        ];
    }
}
