<?php

namespace Webkul\Crm\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Schemas\StageForm;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;

trait ManagesStageKanbanColumns
{
    use ManagesKanbanColumns;

    public function canAddKanbanColumn(): bool
    {
        return StageResource::canCreate();
    }

    public function getAddKanbanColumnLabel(): string
    {
        return __('crm::filament/kanban.add-column.stage');
    }

    protected function kanbanColumnExists(string $name, ?Model $except = null): bool
    {
        return Stage::query()
            ->where('name', $name)
            ->when($except, fn ($query) => $query->whereKeyNot($except->getKey()))
            ->exists();
    }

    protected function createKanbanColumn(string $name): void
    {
        $stage = Stage::query()->create([
            'name'     => $name,
            'is_won'   => false,
            'team_ids' => array_filter([$this->currentKanbanTeamId()]),
        ]);

        $stages = Stage::query()->whereKeyNot($stage->getKey())->orderBy('sort')->get();

        Stage::setNewOrder([
            ...$stages->where('is_won', false)->modelKeys(),
            $stage->getKey(),
            ...$stages->where('is_won', true)->modelKeys(),
        ]);
    }

    protected function currentKanbanTeamId(): ?int
    {
        $teamId = $this->kanbanFilters['team_id'] ?? null;

        if (blank($teamId) || is_array($teamId)) {
            return null;
        }

        return Team::query()->where(owned_by_company(current_company_id()))->whereKey($teamId)->value('id');
    }

    protected function findKanbanColumnRecord(string $value): ?Model
    {
        return ctype_digit($value) ? Stage::query()->find((int) $value) : null;
    }

    protected function canEditKanbanColumnRecord(Model $record): bool
    {
        return StageResource::canEdit($record);
    }

    protected function canDeleteKanbanColumnRecord(Model $record): bool
    {
        return StageResource::canDelete($record);
    }

    protected function getKanbanColumnFormComponents(): array
    {
        return StageForm::components();
    }

    protected function countKanbanColumnRecords(Model $record): int
    {
        return Lead::withTrashed()->withoutGlobalScopes()->where('stage_id', $record->getKey())->count();
    }
}
