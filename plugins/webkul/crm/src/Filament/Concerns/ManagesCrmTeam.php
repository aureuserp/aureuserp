<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Crm\Services\LeadAssignment;
use Webkul\Crm\Services\TeamMembership;
use Webkul\Crm\Settings\CrmSettings;

trait ManagesCrmTeam
{
    protected function beforeFill(): void
    {
        if (! $this->getRecord()?->exists) {
            return;
        }

        app(TeamMembership::class)->sync($this->getRecord());

        $this->getRecord()->unsetRelation('crmMembers')->unsetRelation('crmTeam');
    }

    protected function syncCrmMembership(): void
    {
        $membership = app(TeamMembership::class);

        $membership->syncSalesMembers($this->getRecord());

        $removedFrom = $membership->enforceSingleTeam($this->getRecord());

        $membership->sync($this->getRecord());

        if ($removedFrom) {
            Notification::make()
                ->warning()
                ->title(__('crm::filament/assignment.members.notifications.single-team.title'))
                ->body(__('crm::filament/assignment.members.notifications.removed-from', ['teams' => implode(', ', $removedFrom)]))
                ->send();
        }
    }

    protected function afterSave(): void
    {
        $this->syncCrmMembership();
    }

    protected function afterCreate(): void
    {
        $this->syncCrmMembership();
    }

    protected function getAssignLeadsAction(): Action
    {
        return Action::make('assignLeads')
            ->label(__('crm::filament/assignment.team.assign-leads.label'))
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->visible(fn (): bool => (bool) $this->getRecord()?->exists
                && static::getResource()::canEdit($this->getRecord())
                && app(CrmSettings::class)->use_auto_assignment
                && ! $this->getRecord()->crmTeam?->assignment_optout)
            ->authorize(fn (): bool => (bool) $this->getRecord()?->exists && static::getResource()::canEdit($this->getRecord()))
            ->requiresConfirmation()
            ->modalDescription(__('crm::filament/assignment.team.assign-leads.description'))
            ->action(function (): void {
                $result = app(LeadAssignment::class)->run($this->getRecord());

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/assignment.team.assign-leads.notification', ['count' => $result['salespeople']]))
                    ->send();
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getAssignLeadsAction(),
            ...parent::getHeaderActions(),
        ];
    }
}
