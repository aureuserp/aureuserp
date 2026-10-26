<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Filament\Actions\CrmExportAction;
use Webkul\Crm\Filament\Actions\CrmImportAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Concerns\ShowsTableViewCounts;
use Webkul\Crm\Filament\Exports\OpportunityExporter;
use Webkul\Crm\Filament\Imports\OpportunityImporter;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListOpportunities extends ListRecords
{
    use HasTableViews, ShowsTableViewCounts {
        ShowsTableViewCounts::getCachedFavoriteTableViews insteadof HasTableViews;
        HasTableViews::getCachedFavoriteTableViews as getBaseCachedFavoriteTableViews;
    }

    protected static string $resource = OpportunityResource::class;

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.breadcrumb');
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kanbanView')
                ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.header-actions.kanban.label'))
                ->icon('heroicon-o-view-columns')
                ->color('gray')
                ->url(fn (): string => OpportunityResource::getUrl('index')),
            CrmImportAction::make()
                ->importer(OpportunityImporter::class)
                ->visible(fn (): bool => OpportunityResource::canCreate()),
            CrmExportAction::make()
                ->exporter(OpportunityExporter::class),
            CreateAction::make()
                ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_pipeline' => PresetView::make(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.my-pipeline'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->where('user_id', Auth::id())),

            'open' => PresetView::make(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.open'))
                ->icon('heroicon-s-funnel')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->open()),

            'unassigned' => PresetView::make(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.unassigned'))
                ->icon('heroicon-s-user-minus')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->whereNull('user_id')),

            'won' => PresetView::make(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.won'))
                ->icon('heroicon-s-trophy')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->won()),

            'lost' => PresetView::make(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.lost'))
                ->icon('heroicon-s-hand-thumb-down')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->onlyTrashed()),
        ];
    }
}
