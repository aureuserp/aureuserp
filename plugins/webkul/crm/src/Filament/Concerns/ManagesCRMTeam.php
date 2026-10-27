<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Text;
use Illuminate\Database\Eloquent\Model;
use Webkul\Crm\Filament\Actions\DuplicateRecordsBulkAction;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Services\LeadAssignment;
use Webkul\Crm\Services\TeamMembership;
use Webkul\Crm\Settings\CRMSettings;

trait ManagesCRMTeam
{
    protected function beforeFill(): void
    {
        if (! $this->getRecord()?->exists) {
            return;
        }

        app(TeamMembership::class)->sync($this->getRecord());

        $this->getRecord()->unsetRelation('crmMembers')->unsetRelation('crmTeam');
    }

    protected function syncCRMMembership(): void
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
        $this->syncCRMMembership();
    }

    protected function afterCreate(): void
    {
        $this->syncCRMMembership();
    }

    protected function getAssignLeadsAction(): Action
    {
        return Action::make('assignLeads')
            ->label(__('crm::filament/assignment.team.assign-leads.label'))
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->visible(fn (): bool => (bool) $this->getRecord()?->exists
                && static::getResource()::canEdit($this->getRecord())
                && app(CRMSettings::class)->use_auto_assignment
                && ! $this->getRecord()->crmTeam?->assignment_optout)
            ->authorize(fn (): bool => (bool) $this->getRecord()?->exists && static::getResource()::canEdit($this->getRecord()))
            ->modalHeading(__('crm::filament/assignment.team.assign-leads.heading'))
            ->modalDescription(__('crm::filament/assignment.team.assign-leads.description'))
            ->modalSubmitActionLabel(__('crm::filament/assignment.team.assign-leads.submit'))
            ->modalSubmitAction(fn (Action $action): Action => $action->disabled($this->getAssignmentCandidates() === []))
            ->modalWidth('3xl')
            ->fillForm(fn (): array => ['leads' => array_map('strval', array_keys($this->getAssignmentCandidates()))])
            ->schema(fn (): array => [
                Text::make(fn (): string => $this->getMembersCapacityLine()),
                Text::make(__('crm::filament/assignment.team.assign-leads.nothing'))
                    ->visible(fn (): bool => $this->getAssignmentCandidates() === []),
                CheckboxList::make('leads')
                    ->visible(fn (): bool => $this->getAssignmentCandidates() !== [])
                    ->label(__('crm::filament/assignment.team.assign-leads.records'))
                    ->options(fn (): array => $this->getAssignmentCandidates())
                    ->bulkToggleable()
                    ->searchable()
                    ->noSearchResultsMessage(__('crm::filament/assignment.team.assign-leads.empty'))
                    ->required()
                    ->validationMessages(['required' => __('crm::filament/assignment.team.assign-leads.empty')]),
            ])
            ->action(function (array $data): void {
                $selected = array_map('intval', $data['leads'] ?? []);

                $assigned = app(LeadAssignment::class)->assignSelected($this->getRecord(), $selected);

                Notification::make()
                    ->{$assigned < count($selected) ? 'warning' : 'success'}()
                    ->title(__('crm::filament/assignment.team.assign-leads.notification', ['count' => $assigned]))
                    ->body($assigned < count($selected) ? __('crm::filament/assignment.team.assign-leads.skipped', ['count' => count($selected) - $assigned]) : null)
                    ->send();
            });
    }

    protected ?array $assignmentCandidates = null;

    protected function getAssignmentCandidates(): array
    {
        return $this->assignmentCandidates ??= app(LeadAssignment::class)
            ->candidates($this->getRecord())
            ->with('country')
            ->limit(200)
            ->get()
            ->mapWithKeys(fn (Lead $lead): array => [(string) $lead->getKey() => collect([
                $lead->name,
                $lead->contact_name ?: $lead->email,
                $lead->type?->getLabel(),
                $lead->country?->name,
            ])->filter()->implode(' · ')])
            ->all();
    }

    protected function getMembersCapacityLine(): string
    {
        $members = TeamMember::query()
            ->with('user')
            ->where('team_id', $this->getRecord()->getKey())
            ->where('is_active', true)
            ->where('assignment_optout', false)
            ->get();

        if ($members->isEmpty()) {
            return __('crm::filament/assignment.team.assign-leads.no-members');
        }

        return __('crm::filament/assignment.team.assign-leads.capacity', [
            'members' => $members->map(fn (TeamMember $member): string => $member->user?->name.' '.max(0, $member->assignment_max - $member->leadsAssignedLastMonth()))->implode(' · '),
        ]);
    }

    protected function getTeamRecordActions(): ActionGroup
    {
        $resource = static::getResource();

        return ActionGroup::make([
            $this->getAssignLeadsAction(),
            Action::make('duplicateTeam')
                ->label(__('crm::filament/lead-actions.duplicate.label'))
                ->icon('heroicon-o-document-duplicate')
                ->visible(fn (): bool => $resource::canCreate())
                ->action(function (Action $action) use ($resource): void {
                    $copy = DuplicateRecordsBulkAction::make()->duplicate($this->getRecord());

                    $action->redirect($resource::getUrl('edit', ['record' => $copy]));
                }),
            DeleteAction::make('archiveTeam')
                ->label(__('crm::filament/lead-actions.archive.label'))
                ->icon('heroicon-o-archive-box')
                ->modalHeading(__('crm::filament/lead-actions.archive.label'))
                ->modalDescription(__('crm::filament/lead-actions.archive.description'))
                ->modalSubmitActionLabel(__('crm::filament/lead-actions.archive.label'))
                ->successNotificationTitle(__('crm::filament/lead-actions.archive.notification', ['count' => 1]))
                ->successRedirectUrl($resource::getUrl('index')),
            RestoreAction::make()
                ->label(__('crm::filament/lead-actions.unarchive.label')),
            ForceDeleteAction::make()
                ->label(__('crm::filament/lead-actions.delete.label'))
                ->visible(fn (Model $record): bool => $resource::canForceDelete($record))
                ->successRedirectUrl($resource::getUrl('index')),
        ])
            ->label(__('crm::filament/lead-actions.group.label'))
            ->icon('heroicon-m-cog-6-tooth')
            ->color('gray')
            ->button();
    }

    protected function getHeaderActions(): array
    {
        return [
            ...array_filter(parent::getHeaderActions(), fn ($action): bool => ! $action instanceof DeleteAction),
            $this->getTeamRecordActions(),
        ];
    }
}
