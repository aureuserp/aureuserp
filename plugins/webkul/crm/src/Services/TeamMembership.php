<?php

namespace Webkul\Crm\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Crm\Models\CRMTeam;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

class TeamMembership
{
    public function isMultiTeams(): bool
    {
        return (bool) app(CRMSettings::class)->multi_teams;
    }

    public function sync(Team $team): void
    {
        CRMTeam::query()->firstOrCreate(['team_id' => $team->getKey()]);

        $userIds = DB::table('sales_team_members')->where('team_id', $team->getKey())->pluck('user_id')->map(fn ($id): int => (int) $id);

        TeamMember::query()->where('team_id', $team->getKey())->whereNotIn('user_id', $userIds)->delete();

        $existing = TeamMember::query()->where('team_id', $team->getKey())->pluck('user_id')->map(fn ($id): int => (int) $id);

        foreach ($userIds->diff($existing) as $userId) {
            TeamMember::query()->create(['team_id' => $team->getKey(), 'user_id' => $userId]);
        }
    }

    public function syncSalesMembers(Team $team): void
    {
        $team->members()->sync(TeamMember::query()->where('team_id', $team->getKey())->pluck('user_id')->all());
    }

    public function addMember(Team $team, int $userId, array $attributes = []): array
    {
        return DB::transaction(function () use ($team, $userId, $attributes): array {
            $removedFrom = $this->isMultiTeams() ? [] : $this->removeFromOtherTeams($userId, $team->getKey());

            $team->members()->syncWithoutDetaching([$userId]);

            TeamMember::query()->updateOrCreate(
                ['team_id' => $team->getKey(), 'user_id' => $userId],
                [...$attributes, 'assignment_domain' => AssignmentRules::clean($attributes['assignment_domain'] ?? null)],
            );

            return $removedFrom;
        });
    }

    public function otherTeams(array $userIds, ?int $exceptTeamId = null): array
    {
        if ($this->isMultiTeams()
            || $userIds === []) {
            return [];
        }

        $rows = DB::table('sales_team_members')
            ->whereIn('user_id', $userIds)
            ->when($exceptTeamId, fn ($query) => $query->where('team_id', '!=', $exceptTeamId))
            ->get(['user_id', 'team_id']);

        $teamNames = Team::query()->withTrashed()->whereKey($rows->pluck('team_id')->unique())->pluck('name', 'id');

        $userNames = User::query()->whereKey($rows->pluck('user_id')->unique())->pluck('name', 'id');

        return $rows
            ->groupBy('user_id')
            ->mapWithKeys(fn (Collection $teams, int|string $userId): array => [
                (string) $userNames[$userId] => $teams->map(fn (object $row): string => (string) $teamNames[$row->team_id])->unique()->values()->all(),
            ])
            ->all();
    }

    public function removeMember(TeamMember $member): void
    {
        DB::transaction(function () use ($member): void {
            DB::table('sales_team_members')->where('team_id', $member->team_id)->where('user_id', $member->user_id)->delete();

            $member->delete();
        });
    }

    public function enforceSingleTeam(Team $team): array
    {
        if ($this->isMultiTeams()) {
            return [];
        }

        $removed = [];

        foreach (DB::table('sales_team_members')->where('team_id', $team->getKey())->pluck('user_id') as $userId) {
            $removed = [...$removed, ...$this->removeFromOtherTeams((int) $userId, $team->getKey())];
        }

        return array_values(array_unique($removed));
    }

    protected function removeFromOtherTeams(int $userId, int $exceptTeamId): array
    {
        $teamIds = DB::table('sales_team_members')->where('user_id', $userId)->where('team_id', '!=', $exceptTeamId)->pluck('team_id');

        if ($teamIds->isEmpty()) {
            return [];
        }

        DB::table('sales_team_members')->where('user_id', $userId)->whereIn('team_id', $teamIds)->delete();

        TeamMember::query()->where('user_id', $userId)->whereIn('team_id', $teamIds)->delete();

        return Team::query()->withTrashed()->whereKey($teamIds)->pluck('name')->all();
    }
}
