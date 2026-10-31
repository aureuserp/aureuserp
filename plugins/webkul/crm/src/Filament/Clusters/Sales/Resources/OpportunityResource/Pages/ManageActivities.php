<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\ScheduleActivityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Tables\ActivitiesTable;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Models\Lead;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageActivities extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = OpportunityResource::class;

    protected static string $relationship = 'crmActivities';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;

        return $record instanceof Lead && static::getResource()::canView($record) && ActivityResource::canViewAny();
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity/pages/manage-activities.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity/pages/manage-activities.title');
    }

    public static function navigationItems(Page $page): array
    {
        $record = method_exists($page, 'getRecord') ? $page->getRecord() : null;

        if (! $record instanceof Lead
            || ! static::canAccess(['record' => $record])) {
            return [];
        }

        $count = $record->crmActivities()->count();

        return [
            NavigationItem::make('activities')
                ->label(static::getNavigationLabel())
                ->icon('heroicon-o-clock')
                ->badge($count ? (string) $count : null)
                ->url(static::getResource()::getUrl('activities', ['record' => $record]))
                ->isActiveWhen(fn (): bool => $page instanceof static),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            ScheduleActivityAction::make()->forLead($this->getRecord()),
        ];
    }

    public function table(Table $table): Table
    {
        return ActivitiesTable::configure($table, withinRecord: true)
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['activityType', 'assignedTo']))
            ->emptyStateHeading(__('crm::filament/clusters/sales/resources/opportunity/pages/manage-activities.empty-state.heading'))
            ->emptyStateDescription(__('crm::filament/clusters/sales/resources/opportunity/pages/manage-activities.empty-state.description'))
            ->paginated(false);
    }
}
