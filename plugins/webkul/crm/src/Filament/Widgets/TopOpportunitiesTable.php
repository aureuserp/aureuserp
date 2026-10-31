<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;
use Webkul\Crm\Models\Lead;

class TopOpportunitiesTable extends TableWidget
{
    use HasCRMDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 6;

    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->paginated(false)
            ->recordUrl(fn (Lead $record): ?string => OpportunityResource::canView($record)
                ? OpportunityResource::getUrl('view', ['record' => $record])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('crm::filament/widgets/crm-dashboard.top-opportunities.heading');
    }

    protected function getTableQuery(): Builder
    {
        return $this->openOpportunities()
            ->with(['partner', 'stage', 'user'])
            ->where('crm_leads.expected_revenue', '>', 0)
            ->orderByDesc('crm_leads.expected_revenue')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('crm::filament/widgets/crm-dashboard.top-opportunities.columns.opportunity'))
                ->wrap(),
            TextColumn::make('partner.name')
                ->label(__('crm::filament/widgets/crm-dashboard.top-opportunities.columns.customer'))
                ->placeholder('—'),
            TextColumn::make('stage.name')
                ->label(__('crm::filament/widgets/crm-dashboard.top-opportunities.columns.stage'))
                ->badge(),
            TextColumn::make('user.name')
                ->label(__('crm::filament/widgets/crm-dashboard.top-opportunities.columns.salesperson'))
                ->placeholder('—'),
            TextColumn::make('probability')
                ->label(__('crm::filament/widgets/crm-dashboard.top-opportunities.columns.probability'))
                ->formatStateUsing(fn ($state): string => round((float) $state, 2).' %'),
            TextColumn::make('expected_revenue')
                ->label(__('crm::filament/widgets/crm-dashboard.top-opportunities.columns.expected-revenue'))
                ->money($this->currencyCode()),
        ];
    }
}
