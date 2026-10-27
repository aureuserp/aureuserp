<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Schemas;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;
use Webkul\Crm\Enums\MailAliasContact;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityForm;
use Webkul\Crm\Filament\Support\AssignmentRuleFields;
use Webkul\Crm\Models\CRMTeam;
use Webkul\Crm\Models\MailAlias;
use Webkul\Crm\Models\TeamMember;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Schemas\TeamForm as BaseTeamForm;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        $schema = BaseTeamForm::configure($schema);

        $salesComponents = $schema->getComponents(withHidden: true);

        static::findSalesSelect($salesComponents, 'sales_team_members')?->hidden();

        static::findSalesSelect($salesComponents, 'company_id')
            ?->relationship('company', 'name', fn (Builder $query): Builder => $query->whereKey(app(CompanyContext::class)->activeIds()))
            ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                if (filled($value) && ! in_array((int) $value, array_map('intval', app(CompanyContext::class)->activeIds()), true)) {
                    $fail(__('crm::filament/assignment.team.company-not-active'));
                }
            })
            ->helperText(__('crm::filament/assignment.team.company-helper'));

        return $schema
            ->components([
                Group::make($salesComponents)
                    ->columnSpanFull(),
                static::getAssignmentSection()
                    ->visible(fn (string $operation): bool => $operation === 'create' && TeamResource::usesRuleBasedAssignment())
                    ->columnSpanFull(),
                static::getAliasSection()
                    ->visibleOn('create')
                    ->columnSpanFull(),
                static::getMembersSection()
                    ->visibleOn('create')
                    ->columnSpanFull(),
            ]);
    }

    public static function getAssignmentSection(): Section
    {
        return Section::make(__('crm::filament/assignment.team.section'))
            ->description(__('crm::filament/assignment.team.section-description'))
            ->relationship('crmTeam')
            ->schema([
                Text::make(fn (?CRMTeam $record): string => static::getCapacitySummary((int) $record?->team_id))
                    ->visible(fn (?CRMTeam $record): bool => filled($record?->team_id))
                    ->columnSpanFull(),
                AssignmentRuleFields::make(framed: false, scope: AssignmentRuleFields::unassignedToTeam())
                    ->columnSpanFull(),
            ]);
    }

    protected static function findSalesSelect(array $components, string $name): ?Select
    {
        foreach ($components as $component) {
            if ($component instanceof Select && $component->getName() === $name) {
                return $component;
            }

            if (! $component instanceof Component) {
                continue;
            }

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                if ($found = static::findSalesSelect($childSchema->getComponents(withHidden: true), $name)) {
                    return $found;
                }
            }
        }

        return null;
    }

    public static function getAliasSection(): Section
    {
        return Section::make(__('crm::filament/mail.alias.section'))
            ->description(__('crm::filament/mail.alias.section-description'))
            ->relationship('crmAlias')
            ->columns(3)
            ->schema([
                TextInput::make('alias_name')
                    ->label(__('crm::filament/mail.alias.name'))
                    ->helperText(__('crm::filament/mail.alias.name-helper'))
                    ->regex('/^[a-zA-Z0-9]+([._-][a-zA-Z0-9]+)*$/')
                    ->validationMessages(['regex' => __('crm::filament/mail.alias.name-invalid'), 'unique' => __('crm::filament/mail.alias.name-taken')])
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
                    ->label(__('crm::filament/mail.alias.domain'))
                    ->helperText(__('crm::filament/mail.alias.domain-helper'))
                    ->placeholder('yourcompany.com')
                    ->regex('/^@?(?!-)[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)+$/')
                    ->validationMessages(['regex' => __('crm::filament/mail.alias.domain-invalid')])
                    ->requiredWith('alias_name')
                    ->dehydrateStateUsing(fn (?string $state): ?string => MailAlias::normalizeDomain($state))
                    ->maxLength(255),
                Select::make('alias_contact')
                    ->label(__('crm::filament/mail.alias.contact'))
                    ->options(MailAliasContact::class)
                    ->helperText(fn (Get $get): string => ($get('alias_contact') ?? MailAliasContact::EVERYONE)->getDescription())
                    ->default(MailAliasContact::EVERYONE)
                    ->selectablePlaceholder(false)
                    ->live()
                    ->required(),
            ]);
    }

    public static function getNewSalespersonFields(): array
    {
        return [
            TextInput::make('name')
                ->label(__('security::filament/resources/user.form.sections.general-information.fields.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label(__('security::filament/resources/user.form.sections.general-information.fields.email'))
                ->email()
                ->required()
                ->unique(User::class, 'email')
                ->maxLength(255),
            TextInput::make('password')
                ->label(__('security::filament/resources/user.form.sections.general-information.fields.password'))
                ->password()
                ->revealable()
                ->required()
                ->rule('min:8')
                ->maxLength(255),
            TextInput::make('password_confirmation')
                ->label(__('security::filament/resources/user.form.sections.general-information.fields.password-confirmation'))
                ->password()
                ->revealable()
                ->required()
                ->same('password'),
            Select::make('roles')
                ->label(__('security::filament/resources/user.form.sections.permissions.fields.roles'))
                ->options(fn (): array => Role::query()->orderBy('name')->pluck('name', 'id')->all())
                ->multiple()
                ->required()
                ->searchable()
                ->columnSpanFull(),
        ];
    }

    public static function getMembersSection(): Section
    {
        return Section::make(__('crm::filament/assignment.members.title'))
            ->description(__('crm::filament/assignment.members.description'))
            ->schema([
                Repeater::make('crmMembers')
                    ->relationship('crmMembers')
                    ->hiddenLabel()
                    ->schema(static::getMemberFields(fn (Get $get): mixed => $get('../../company_id')))
                    ->columns(4)
                    ->itemLabel(fn (array $state): ?string => filled($state['user_id'] ?? null) ? User::query()->whereKey($state['user_id'])->value('name') : null)
                    ->collapsible()
                    ->collapsed(fn (string $operation): bool => $operation !== 'create')
                    ->defaultItems(0)
                    ->reorderable(false)
                    ->addActionLabel(__('crm::filament/assignment.members.actions.add')),
            ]);
    }

    public static function getMemberFields(Closure $companyId, ?Closure $excludeUserIds = null, ?Closure $teamId = null): array
    {
        return [
            Select::make('user_id')
                ->label(__('crm::filament/assignment.members.fields.salesperson'))
                ->relationship('user', 'name', fn (Builder $query, Get $get): Builder => OpportunityForm::scopeSalespeople($query, $companyId($get))
                    ->when($excludeUserIds, fn (Builder $query): Builder => $query->whereKeyNot($excludeUserIds()))
                    ->orderBy('name'))
                ->searchable()
                ->preload()
                ->required()
                ->distinct()
                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                ->live()
                ->createOptionForm(fn (): array => static::getNewSalespersonFields())
                ->createOptionModalHeading(__('crm::filament/assignment.members.new-salesperson.heading'))
                ->createOptionAction(fn (Action $action): Action => $action
                    ->tooltip(__('crm::filament/assignment.members.new-salesperson.heading'))
                    ->visible(fn (): bool => UserResource::canCreate())
                    ->authorize(fn (): bool => UserResource::canCreate()))
                ->createOptionUsing(fn (array $data, Get $get): int => TeamResource::createSalesperson($data, $companyId($get))),
            Text::make(fn (?TeamMember $record): string => __('crm::filament/assignment.team.capacity', [
                'assigned' => $record?->leadsAssignedLastMonth() ?? 0,
                'max'      => (int) $record?->assignment_max,
            ]))
                ->visible(fn (?TeamMember $record): bool => TeamResource::usesRuleBasedAssignment() && (bool) $record?->exists),
            TextInput::make('assignment_max')
                ->visible(fn (): bool => TeamResource::usesRuleBasedAssignment())
                ->label(__('crm::filament/assignment.members.fields.capacity'))
                ->helperText(__('crm::filament/assignment.members.fields.capacity-helper'))
                ->numeric()
                ->integer()
                ->minValue(0)
                ->default(30)
                ->required(),
            Toggle::make('assignment_optout')
                ->visible(fn (): bool => TeamResource::usesRuleBasedAssignment())
                ->label(__('crm::filament/assignment.team.skip-assignment'))
                ->helperText(__('crm::filament/assignment.members.fields.paused-helper'))
                ->inline(false),
            Toggle::make('is_active')
                ->label(__('crm::filament/assignment.members.fields.active'))
                ->default(true)
                ->inline(false),
            AssignmentRuleFields::make(scope: AssignmentRuleFields::unassignedInTeam($teamId ?? fn (): ?int => null))
                ->visible(fn (): bool => TeamResource::usesRuleBasedAssignment())
                ->columnSpanFull(),
        ];
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
            'max'      => (int) $members->sum('assignment_max'),
        ]);
    }
}
