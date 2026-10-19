<?php

namespace Webkul\Crm\Filament\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\ScheduleActivityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Tables\ActivitiesTable;
use Webkul\Crm\Models\Lead;

/**
 * Planned activities of a lead or opportunity, shown on its view and edit pages.
 * Uses the same columns and actions (Done, Reschedule, Cancel) as My Activities.
 */
class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'crmActivities';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-clock';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('crm::filament/relation-managers/activities.title');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->crmActivities()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return ActivityResource::canViewAny();
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        /** @var Lead $lead */
        $lead = $this->getOwnerRecord();

        return ActivitiesTable::configure($table, withinRecord: true)
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['activityType', 'assignedTo']))
            ->headerActions([
                ScheduleActivityAction::make()
                    ->forLead($lead),
            ])
            ->emptyStateHeading(__('crm::filament/relation-managers/activities.empty-state.heading'))
            ->emptyStateDescription(__('crm::filament/relation-managers/activities.empty-state.description'))
            ->paginated(false);
    }
}
