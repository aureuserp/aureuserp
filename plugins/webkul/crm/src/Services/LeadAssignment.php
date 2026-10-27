<?php

namespace Webkul\Crm\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Webkul\Crm\Enums\LeadConversionAction;
use Webkul\Crm\Enums\LeadConversionCustomer;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Sale\Models\Team;
use Webkul\Support\Models\Scopes\CompanyScope;

class LeadAssignment
{
    public const BATCH = 1000;

    public function __construct(protected TeamMembership $membership) {}

    public function run(?Team $onlyTeam = null): array
    {
        if (! app(CRMSettings::class)->use_auto_assignment) {
            return ['teams' => 0, 'salespeople' => 0];
        }

        $teams = $this->eligibleTeams($onlyTeam);

        if ($teams->isEmpty()) {
            return ['teams' => 0, 'salespeople' => 0];
        }

        $teamsAssigned = $onlyTeam ? 0 : $this->assignTeams($teams);

        $salespeopleAssigned = 0;

        foreach ($teams as $team) {
            $salespeopleAssigned += $this->assignSalespeople($team);
        }

        return ['teams' => $teamsAssigned, 'salespeople' => $salespeopleAssigned];
    }

    public function candidates(Team $team): Builder
    {
        $team->loadMissing('crmTeam');

        $settings = $team->crmTeam;

        return $this->openLeads()
            ->whereNull('user_id')
            ->where(fn (Builder $query): Builder => $query
                ->where('team_id', $team->getKey())
                ->orWhere(fn (Builder $query): Builder => AssignmentRules::apply(
                    $query->whereNull('team_id')->when($team->company_id, fn (Builder $query): Builder => $query->where('company_id', $team->company_id)),
                    $settings?->assignment_domain,
                )))
            ->when($settings && ! $settings->use_leads, fn (Builder $query): Builder => $query->where('type', '!=', LeadType::LEAD))
            ->when($settings && ! $settings->use_opportunities, fn (Builder $query): Builder => $query->where('type', '!=', LeadType::OPPORTUNITY));
    }

    public function assignSelected(Team $team, array $leadIds): int
    {
        if (! app(CRMSettings::class)->use_auto_assignment || $leadIds === []) {
            return 0;
        }

        $team = $this->eligibleTeams($team)->first();

        if (! $team) {
            return 0;
        }

        $assigned = 0;

        foreach ($this->candidates($team)->whereKey($leadIds)->get() as $lead) {
            if (blank($lead->team_id)) {
                $lead->team_id = $team->getKey();
                $lead->save();
            }

            $assigned += $this->assignSalespeople($team, $lead);
        }

        return $assigned;
    }

    public function assignLead(Lead $lead): void
    {
        if (! app(CRMSettings::class)->use_auto_assignment || ! $lead->exists || $lead->trashed()) {
            return;
        }

        $lead->loadMissing(['tags', 'stage']);

        if ($lead->stage?->is_won) {
            return;
        }

        $teams = $this->eligibleTeams(null, syncMembers: false);

        if (blank($lead->team_id)) {
            $team = $this->pickTeam($lead, $teams, $teams->mapWithKeys(fn (Team $team): array => [$team->getKey() => $this->teamCapacity($team)]));

            if (! $team) {
                return;
            }

            $lead->team_id = $team->getKey();
            $lead->save();
        }

        if (blank($lead->user_id) && ($team = $teams->firstWhere('id', $lead->team_id))) {
            $this->assignSalespeople($team, $lead);
        }
    }

    protected function eligibleTeams(?Team $onlyTeam, bool $syncMembers = true): Collection
    {
        $teams = Team::query()
            ->withoutGlobalScopes([CompanyScope::class])
            ->where('is_active', true)
            ->when($onlyTeam, fn (Builder $query): Builder => $query->whereKey($onlyTeam->getKey()))
            ->get();

        if ($syncMembers) {
            $teams->each(fn (Team $team) => $this->membership->sync($team));
        }

        return $teams
            ->each(fn (Team $team) => $team->load(['crmTeam', 'crmMembers']))
            ->reject(fn (Team $team): bool => (bool) $team->crmTeam?->assignment_optout)
            ->values();
    }

    protected function assignTeams(Collection $teams): int
    {
        $remaining = $teams->mapWithKeys(fn (Team $team): array => [$team->getKey() => $this->teamCapacity($team)]);

        $assigned = 0;

        foreach ($this->openLeads()->whereNull('team_id')->limit(self::BATCH)->get() as $lead) {
            $team = $this->pickTeam($lead, $teams, $remaining);

            if (! $team) {
                continue;
            }

            $lead->team_id = $team->getKey();
            $lead->save();

            if (blank($lead->user_id)) {
                $remaining[$team->getKey()] = $remaining[$team->getKey()] - 1;
            }

            $assigned++;
        }

        return $assigned;
    }

    protected function pickTeam(Lead $lead, Collection $teams, Collection $remaining): ?Team
    {
        $matching = $teams->filter(fn (Team $team): bool => (blank($team->company_id) || (int) $team->company_id === (int) $lead->company_id)
            && $this->acceptsType($team, $lead)
            && AssignmentRules::matches($lead, $team->crmTeam?->assignment_domain));

        if (filled($lead->user_id)) {
            $salespersonTeams = $matching->filter(fn (Team $team): bool => $team->crmMembers->contains('user_id', $lead->user_id));

            return ($salespersonTeams->isNotEmpty() ? $salespersonTeams : $matching)->first();
        }

        return $matching
            ->filter(fn (Team $team): bool => ($remaining[$team->getKey()] ?? 0) > 0)
            ->sortByDesc(fn (Team $team): int => $remaining[$team->getKey()])
            ->first();
    }

    protected function assignSalespeople(Team $team, ?Lead $onlyLead = null): int
    {
        $members = $team->crmMembers
            ->filter(fn (TeamMember $member): bool => $member->is_active && ! $member->assignment_optout && $member->assignment_max > 0)
            ->values();

        if ($members->isEmpty()) {
            return 0;
        }

        $load = $members->mapWithKeys(fn (TeamMember $member): array => [$member->getKey() => $member->leadsAssignedLastMonth()]);

        $assigned = 0;

        $leads = $onlyLead
            ? collect([$onlyLead])
            : $this->openLeads()->where('team_id', $team->getKey())->whereNull('user_id')->limit(self::BATCH)->get();

        foreach ($leads as $lead) {
            $candidates = $members->filter(fn (TeamMember $member): bool => $load[$member->getKey()] < $member->assignment_max
                && AssignmentRules::matches($lead, $member->assignment_domain));

            if ($candidates->isEmpty()) {
                continue;
            }

            $member = $candidates
                ->sortBy(fn (TeamMember $member): array => [$load[$member->getKey()] / $member->assignment_max, $member->getKey()])
                ->first();

            $lead->user_id = $member->user_id;
            $lead->save();

            if ($lead->isLead() && ($team->crmTeam?->use_opportunities ?? true)) {
                app(LeadConverter::class)->convert($lead, [
                    'action'   => LeadConversionAction::CONVERT,
                    'customer' => LeadConversionCustomer::NONE,
                    'user_id'  => $member->user_id,
                    'team_id'  => $team->getKey(),
                ]);
            }

            $load[$member->getKey()] = $load[$member->getKey()] + 1;

            $assigned++;
        }

        return $assigned;
    }

    protected function teamCapacity(Team $team): int
    {
        return (int) $team->crmMembers
            ->filter(fn (TeamMember $member): bool => $member->is_active && ! $member->assignment_optout)
            ->sum(fn (TeamMember $member): int => max(0, $member->assignment_max - $member->leadsAssignedLastMonth()));
    }

    protected function acceptsType(Team $team, Lead $lead): bool
    {
        $settings = $team->crmTeam;

        if (! $settings) {
            return true;
        }

        return $lead->isLead() ? $settings->use_leads : $settings->use_opportunities;
    }

    protected function openLeads(): Builder
    {
        return Lead::query()
            ->withoutGlobalScopes([CompanyScope::class])
            ->with('tags')
            ->open()
            ->orderByDesc('priority')
            ->orderBy('created_at')
            ->orderBy('id');
    }
}
