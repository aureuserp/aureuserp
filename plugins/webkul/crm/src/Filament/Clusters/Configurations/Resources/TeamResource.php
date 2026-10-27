<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\CreateTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\EditTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ListTeams;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ManageTeamAlias;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ManageTeamAssignment;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ManageTeamMembers;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ViewTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Schemas\TeamForm;
use Webkul\Crm\Filament\Concerns\HasCRMAuthorization;
use Webkul\Crm\Filament\Exports\TeamExporter;
use Webkul\Crm\Filament\Support\AssignmentRuleFields;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Policies\TeamPolicy;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource as BaseTeamResource;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource as BaseUserResource;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

class TeamResource extends BaseTeamResource
{
    use HasCRMAuthorization;

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

    public static function getCRMPolicy(): string
    {
        return TeamPolicy::class;
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->toolbarActions([
                RecordBulkActions::make(exporter: TeamExporter::class, archivable: true, copyRelations: ['members']),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return TeamForm::configure($schema);
    }

    public static function createSalesperson(array $data, mixed $companyId): int
    {
        abort_unless(BaseUserResource::canCreate(), 403);

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

    public static function usesRuleBasedAssignment(): bool
    {
        return (bool) app(CRMSettings::class)->use_auto_assignment;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = $page->generateNavigationItems([
            ViewTeam::class,
            EditTeam::class,
            ManageTeamAssignment::class,
            ManageTeamMembers::class,
            ManageTeamAlias::class,
        ]);

        $team = method_exists($page, 'getRecord') ? $page->getRecord() : null;

        if (! $team instanceof Model) {
            return $items;
        }

        $badges = [
            ManageTeamMembers::getNavigationLabel()    => fn (): int => TeamMember::query()->where('team_id', $team->getKey())->count(),
            ManageTeamAssignment::getNavigationLabel() => fn (): int => AssignmentRuleFields::matchingQuery($team->crmTeam?->assignment_domain ?? [], AssignmentRuleFields::unassignedToTeam())->count(),
        ];

        return array_map(
            fn (NavigationItem $item): NavigationItem => isset($badges[$item->getLabel()])
                ? $item->badge($badges[$item->getLabel()]())
                : $item,
            $items,
        );
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListTeams::route('/'),
            'create'     => CreateTeam::route('/create'),
            'view'       => ViewTeam::route('/{record}'),
            'edit'       => EditTeam::route('/{record}/edit'),
            'assignment' => ManageTeamAssignment::route('/{record}/assignment-rules'),
            'members'    => ManageTeamMembers::route('/{record}/members'),
            'alias'      => ManageTeamAlias::route('/{record}/email-alias'),
        ];
    }
}
