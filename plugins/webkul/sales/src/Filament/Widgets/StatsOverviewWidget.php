<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    public function getHeading(): ?string
    {
        return __('sales::filament/pages/stats-overview.heading.title');
    }

    protected function getStats(): array
    {
        $baseQuery = $this->getFilteredQuery();

        $statsConfig = $this->getStatsConfig();

        $stats = [];

        foreach ($statsConfig as $config) {
            $count = $this->countOrdersByState(clone $baseQuery, $config['state']->value);

            $stats[] = Stat::make(__($config['title']), number_format($count))
                ->description(__($config['description']))
                ->icon($config['icon'])
                ->color($config['color']);
        }

        return $stats;
    }

    /**
     * 🔹 Build the base query with filters.
     */
    protected function getFilteredQuery()
    {
        $filters = $this->filters;

        $query = Order::query();

        if (! empty($filters['start_date'])) {
            $query->whereDate('date_order', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('date_order', '<=', $filters['end_date']);
        }

        if (! empty($filters['salesperson_id'])) {
            $query->where('user_id', $filters['salesperson_id']);
        }

        return $query;
    }

    /**
     * 🔹 Centralized stats configuration.
     */
    protected function getStatsConfig(): array
    {
        return [
            [
                'state'       => OrderState::SENT,
                'title'       => 'sales::filament/pages/stats-overview.title.total-sent-quotation',
                'description' => 'sales::filament/pages/stats-overview.descriptions.total-sent-quotation',
                'icon'        => 'heroicon-o-shopping-cart',
                'color'       => 'primary',
            ],
            [
                'state'       => OrderState::SALE,
                'title'       => 'sales::filament/pages/stats-overview.title.total-quotation-confirm-by-customer',
                'description' => 'sales::filament/pages/stats-overview.descriptions.total-quotation-confirm-by-customer',
                'icon'        => 'heroicon-o-currency-dollar',
                'color'       => 'success',
            ],
            [
                'state'       => OrderState::DRAFT,
                'title'       => 'sales::filament/pages/stats-overview.title.total-draft-quotation',
                'description' => 'sales::filament/pages/stats-overview.descriptions.total-draft-quotation',
                'icon'        => 'heroicon-o-chart-bar',
                'color'       => 'info',
            ],
            [
                'state'       => OrderState::CANCEL,
                'title'       => 'sales::filament/pages/stats-overview.title.cancel-order',
                'description' => 'sales::filament/pages/stats-overview.descriptions.cancel-order',
                'icon'        => 'heroicon-o-x-circle',
                'color'       => 'danger',
            ],
            [
                'state'       => OrderState::CANCEL,
                'title'       => 'sales::filament/pages/stats-overview.title.return-order',
                'description' => 'sales::filament/pages/stats-overview.descriptions.return-order',
                'icon'        => 'heroicon-o-document-text',
                'color'       => 'warning',
            ],
        ];
    }

    /**
     * 🔹 Reusable order count by state.
     */
    protected function countOrdersByState($query, string $state): int
    {
        return $query->where('state', $state)->count();
    }
}
