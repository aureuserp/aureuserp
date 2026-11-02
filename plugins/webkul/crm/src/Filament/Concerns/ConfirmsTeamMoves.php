<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Webkul\Crm\Services\TeamMembership;

trait ConfirmsTeamMoves
{
    protected bool $isTeamMoveConfirmed = false;

    abstract protected function afterTeamMoveConfirmed(array $arguments): void;

    protected function confirmsTeamMove(array $userIds, ?int $teamId = null, array $arguments = []): bool
    {
        if ($this->isTeamMoveConfirmed) {
            return false;
        }

        $otherTeams = app(TeamMembership::class)->otherTeams(array_values(array_filter(array_map('intval', $userIds))), $teamId);

        if ($otherTeams === []) {
            return false;
        }

        $this->mountAction('confirmTeamMove', [...$arguments, 'otherTeams' => $otherTeams]);

        return true;
    }

    public function confirmTeamMoveAction(): Action
    {
        return Action::make('confirmTeamMove')
            ->requiresConfirmation()
            ->color('warning')
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalIconColor('warning')
            ->modalHeading(fn (array $arguments): string => trans_choice('crm::filament/assignment.members.move.heading', count($arguments['otherTeams'] ?? []), ['count' => count($arguments['otherTeams'] ?? [])]))
            ->modalDescription(fn (array $arguments): string => $this->getTeamMoveDescription($arguments['otherTeams'] ?? []))
            ->modalSubmitActionLabel(__('crm::filament/assignment.members.move.submit'))
            ->action(function (array $arguments): void {
                $this->isTeamMoveConfirmed = true;

                $this->afterTeamMoveConfirmed($arguments);
            });
    }

    protected function getTeamMoveDescription(array $otherTeams): string
    {
        $lines = collect($otherTeams)
            ->map(fn (array $teams, int|string $name): string => __('crm::filament/assignment.members.move.member', ['name' => $name, 'teams' => implode(', ', $teams)]))
            ->values();

        $question = count($otherTeams) === 1
            ? __('crm::filament/assignment.members.move.question-one', ['name' => array_key_first($otherTeams), 'teams' => implode(', ', reset($otherTeams))])
            : __('crm::filament/assignment.members.move.question-many');

        return $lines->push($question)->implode(' ');
    }
}
