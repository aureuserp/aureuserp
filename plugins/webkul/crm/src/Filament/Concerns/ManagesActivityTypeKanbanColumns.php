<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityType;
use Webkul\Support\Enums\ActivityChainingType;
use Webkul\Support\Enums\ActivityDecorationType;
use Webkul\Support\Enums\ActivityDelayFrom;
use Webkul\Support\Enums\ActivityDelayUnit;
use Webkul\Support\Enums\ActivityTypeAction;
use Webkul\Support\Models\Scopes\CompanyScope;

trait ManagesActivityTypeKanbanColumns
{
    use ManagesKanbanColumns;

    public function canAddKanbanColumn(): bool
    {
        return ActivityTypeResource::canCreate();
    }

    public function getAddKanbanColumnLabel(): string
    {
        return __('crm::filament/kanban.add-column.activity-type');
    }

    protected function kanbanColumnExists(string $name, ?Model $except = null): bool
    {
        return ActivityType::query()
            ->where('plugin', 'crm')
            ->where('name', $name)
            ->when($except, fn ($query) => $query->whereKeyNot($except->getKey()))
            ->exists();
    }

    protected function createKanbanColumn(string $name, array $attributes = []): Model
    {
        return ActivityType::query()->create([
            'name'            => $name,
            'summary'         => $name,
            'plugin'          => 'crm',
            'icon'            => 'heroicon-o-clock',
            'delay_count'     => 0,
            'delay_unit'      => ActivityDelayUnit::DAYS,
            'delay_from'      => ActivityDelayFrom::CURRENT_DATE,
            'category'        => ActivityTypeAction::DEFAULT,
            'decoration_type' => ActivityDecorationType::ALERT,
            'chaining_type'   => ActivityChainingType::SUGGEST,
            'is_active'       => true,
        ]);
    }

    protected function kanbanColumnModel(): string
    {
        return ActivityType::class;
    }

    protected function findKanbanColumnRecord(string $value): ?Model
    {
        return ctype_digit($value)
            ? ActivityType::query()->where('plugin', 'crm')->find((int) $value)
            : null;
    }

    protected function canEditKanbanColumnRecord(Model $record): bool
    {
        return ActivityTypeResource::canEdit($record);
    }

    protected function canDeleteKanbanColumnRecord(Model $record): bool
    {
        return ActivityTypeResource::canDelete($record);
    }

    protected function getKanbanColumnFormComponents(): array
    {
        return [
            TextInput::make('name')
                ->label(__('crm::filament/kanban.column-actions.name'))
                ->required()
                ->maxLength(255),
        ];
    }

    protected function countKanbanColumnRecords(Model $record): int
    {
        return Activity::query()->withoutGlobalScopes([CompanyScope::class])->where('activity_type_id', $record->getKey())->count();
    }
}
