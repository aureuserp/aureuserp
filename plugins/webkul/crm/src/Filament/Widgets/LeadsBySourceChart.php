<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;
use Webkul\Crm\Filament\Widgets\Concerns\StylesCRMDashboardCharts;
use Webkul\Support\Models\UTMSource;

class LeadsBySourceChart extends ChartWidget
{
    use HasCRMDashboardFilters, HasWidgetShield, StylesCRMDashboardCharts;

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('crm::filament/widgets/crm-dashboard.sources.heading');
    }

    protected function getData(): array
    {
        $counts = $this->createdBetween($this->crmRecords(), null, null)
            ->selectRaw('crm_leads.utm_source_id, COUNT(*) as total')
            ->groupBy('crm_leads.utm_source_id')
            ->orderByDesc('total')
            ->pluck('total', 'utm_source_id');

        $names = UTMSource::query()->whereIn('id', $counts->keys()->filter())->pluck('name', 'id');

        $labels = $counts->keys()->map(fn ($sourceId): string => $names[$sourceId] ?? __('crm::filament/widgets/crm-dashboard.sources.none'))->all();

        return [
            'datasets' => [$this->barDataset(
                __('crm::filament/widgets/crm-dashboard.sources.count'),
                $counts->values()->map(fn ($total): int => (int) $total)->all(),
                $counts->keys()->values()->map(fn ($sourceId, int $index): string => self::PALETTE[$index % count(self::PALETTE)])->all(),
            )],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return $this->barOptions();
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
