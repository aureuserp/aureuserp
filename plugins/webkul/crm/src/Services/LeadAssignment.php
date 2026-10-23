<?php

namespace Webkul\Crm\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LeadConversion;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Settings\CrmSettings;
use Webkul\Sale\Models\Team;
use Webkul\Support\Models\Scopes\CompanyScope;

class LeadAssignment
{
    public const BATCH = 1000;

    public function __construct(protected TeamMembership $membership) {}

    public function run(?Team $onlyTeam = null): array
    {
        if (! app(CrmSettings::class)->use_auto_assignment) {
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

    protected function eligibleTeams(?Team $onlyTeam): Collection
    {
        $teams = Team::query()
            ->withoutGlobalScopes([CompanyScope::class])
            ->where('is_active', true)
            ->when($onlyTeam, fn (Builder $query): Builder => $query->whereKey($onlyTeam->getKey()))
            ->get();

        $teams->each(fn (Team $team) => $this->membership->sync($team));

        return $teams
            ->each(fn (Team $team) => $team->load(['crmTeam', 'crmMembers']))
            ->reject(fn (Team $team): bool => (bool) $team->crmTeam?->assignment_optout)
            ->values();
    }

    protected function assignTeams(Collection $teams): int
    {
        $remaining = $teams->mapWithKeys(fn (Team $team): array => [$team->getKey() => $this->teamCapacity($team)]);

        $assigned = 0;

        foreach ($this->openLeads()->whereNull('team_id')->whereNull('user_id')->limit(self::BATCH)->get() as $lead) {
            $candidates = $teams->filter(fn (Team $team): bool => $remaining[$team->getKey()] > 0
                && (blank($team->company_id) || (int) $team->company_id === (int) $lead->company_id)
                && $this->acceptsType($team, $lead)
                && AssignmentRules::matches($lead, $team->crmTeam?->assignment_domain));

            if ($candidates->isEmpty()) {
                continue;
            }

            $team = $candidates->sortByDesc(fn (Team $team): int => $remaining[$team->getKey()])->first();

            $lead->team_id = $team->getKey();
            $lead->save();

            $remaining[$team->getKey()] = $remaining[$team->getKey()] - 1;

            $assigned++;
        }

        return $assigned;
    }

    protected function assignSalespeople(Team $team): int
    {
        $members = $team->crmMembers
            ->filter(fn (TeamMember $member): bool => $member->is_active && ! $member->assignment_optout && $member->assignment_max > 0)
            ->values();

        if ($members->isEmpty()) {
            return 0;
        }

        $load = $members->mapWithKeys(fn (TeamMember $member): array => [$member->getKey() => $member->leadsAssignedLastMonth()]);

        $assigned = 0;

        foreach ($this->openLeads()->where('team_id', $team->getKey())->whereNull('user_id')->limit(self::BATCH)->get() as $lead) {
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
                    'action'   => LeadConversion::ACTION_CONVERT,
                    'customer' => LeadConversion::CUSTOMER_NONE,
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
