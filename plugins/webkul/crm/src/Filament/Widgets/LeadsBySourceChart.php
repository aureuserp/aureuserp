<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;
use Webkul\Support\Models\UTMSource;

class LeadsBySourceChart extends DashboardChartWidget
{
    use HasCRMDashboardFilters, HasWidgetShield;

    protected const TOP_SLICES = 3;

    protected static ?int $sort = 3;

    protected ?array $resolvedData = null;

    public function getHeading(): string|Htmlable|null
    {
        return __('crm::filament/widgets/crm-dashboard.sources.heading');
    }

    protected function resolveChartData(): array
    {
        if ($this->resolvedData !== null) {
            return $this->resolvedData;
        }

        $counts = $this->createdBetween($this->crmRecords(), null, null)
            ->selectRaw('crm_leads.utm_source_id, COUNT(*) as total')
            ->groupBy('crm_leads.utm_source_id')
            ->orderByDesc('total')
            ->pluck('total', 'utm_source_id');

        $names = UTMSource::query()->whereIn('id', $counts->keys()->filter())->pluck('name', 'id');

        $labels = [];
        $data = [];

        foreach ($counts->take(self::TOP_SLICES) as $sourceId => $total) {
            $labels[] = $names[$sourceId] ?? __('crm::filament/widgets/crm-dashboard.sources.none');
            $data[] = (int) $total;
        }

        $other = (int) $counts->skip(self::TOP_SLICES)->sum();

        if ($other > 0) {
            $labels[] = __('crm::filament/widgets/crm-dashboard.sources.other');
            $data[] = $other;
        }

        return $this->resolvedData = ['labels' => $labels, 'data' => $data];
    }

    protected function getData(): array
    {
        ['labels' => $labels, 'data' => $data] = $this->resolveChartData();

        return [
            'datasets' => [
                [
                    'label'           => __('crm::filament/widgets/crm-dashboard.sources.count'),
                    'data'            => $data,
                    'backgroundColor' => array_slice($this->chartRamp(), 0, max(count($data), 1)),
                    'borderWidth'     => 0,
                    'hoverOffset'     => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return $this->doughnutChartOptions(
            number_format(array_sum($this->resolveChartData()['data'])),
            __('crm::filament/widgets/crm-dashboard.sources.count'),
        );
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
