<?php

namespace Webkul\Crm\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Schemas\StageForm;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;

trait ManagesStageKanbanColumns
{
    use ManagesKanbanColumns;

    abstract protected function kanbanTeamFilterId(): ?int;

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

    protected function createKanbanColumn(string $name, array $attributes = []): Model
    {
        $stage = Stage::query()->create([
            ...Arr::only($attributes, ['requirements']),
            'name'   => $name,
            'is_won' => (bool) ($attributes['is_won'] ?? false),
        ]);

        $stage->teams()->sync(array_filter([$this->currentKanbanTeamId()]));

        $stages = Stage::query()->whereKeyNot($stage->getKey())->orderBy('sort')->get();

        Stage::setNewOrder($stage->is_won
            ? [...$stages->modelKeys(), $stage->getKey()]
            : [
                ...$stages->where('is_won', false)->modelKeys(),
                $stage->getKey(),
                ...$stages->where('is_won', true)->modelKeys(),
            ]);

        return $stage;
    }

    protected function currentKanbanTeamId(): ?int
    {
        $teamId = $this->kanbanTeamFilterId();

        if (blank($teamId)) {
            return null;
        }

        return Team::query()->where(owned_by_company(current_company_id()))->whereKey($teamId)->value('id');
    }

    protected function kanbanColumnModel(): string
    {
        return Stage::class;
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
        return StageForm::components(defaultTeamId: $this->currentKanbanTeamId());
    }

    protected function countKanbanColumnRecords(Model $record): int
    {
        return $record instanceof Stage ? $record->recordsCount() : 0;
    }
}
