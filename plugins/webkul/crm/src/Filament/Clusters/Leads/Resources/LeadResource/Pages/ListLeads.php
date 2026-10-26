<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Webkul\Crm\Filament\Actions\CrmExportAction;
use Webkul\Crm\Filament\Actions\CrmImportAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Tables\LeadsTable;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Filament\Concerns\HasCrmTableViews;
use Webkul\Crm\Filament\Exports\LeadExporter;
use Webkul\Crm\Filament\Imports\LeadImporter;
use Webkul\TableViews\Filament\Components\PresetView;

class ListLeads extends ListRecords
{
    use HasCrmClusterNavigation, HasCrmTableViews;

    public const DEFAULT_LAYOUT = 'list';

    public const LAYOUTS = [
        'list' => 'heroicon-o-list-bullet',
        'grid' => 'heroicon-o-squares-2x2',
    ];

    protected static string $resource = LeadResource::class;

    #[Url(as: 'layout')]
    public string $viewLayout = self::DEFAULT_LAYOUT;

    public function mount(): void
    {
        if (! array_key_exists($this->viewLayout, self::LAYOUTS)) {
            $this->viewLayout = self::DEFAULT_LAYOUT;
        }

        parent::mount();
    }

    public function isGridLayout(): bool
    {
        return $this->viewLayout === 'grid';
    }

    public function table(Table $table): Table
    {
        return LeadsTable::configure($table, grid: $this->isGridLayout());
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/leads/resources/lead.navigation.title');
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/leads/resources/lead.views.'.$this->viewLayout);
    }

    protected function getHeaderActions(): array
    {
        return [
            ...LeadResource::getViewSwitcherActions($this->viewLayout),
            CrmImportAction::make()
                ->importer(LeadImporter::class)
                ->visible(fn (): bool => LeadResource::canCreate()),
            CrmExportAction::make()
                ->exporter(LeadExporter::class),
            CreateAction::make()
                ->label(__('crm::filament/clusters/leads/resources/lead.actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_leads' => PresetView::make(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.tabs.my-leads'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->where('user_id', Auth::id())),

            'unassigned' => PresetView::make(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.tabs.unassigned'))
                ->icon('heroicon-s-user-minus')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->whereNull('user_id')),

            'all' => PresetView::make(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.tabs.all'))
                ->icon('heroicon-s-inbox-stack')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()),

            'lost' => PresetView::make(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.tabs.lost'))
                ->icon('heroicon-s-hand-thumb-down')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->onlyTrashed()),
        ];
    }
}
