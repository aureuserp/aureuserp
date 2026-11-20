<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Widgets;

use Filament\Widgets\ChartWidget;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Filament\Support\SimilarLeads;
use Webkul\Crm\Filament\Widgets\Concerns\StylesCRMDashboardCharts;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;

class SimilarLeadsChart extends ChartWidget
{
    use StylesCRMDashboardCharts;

    public ?int $leadId = null;

    protected ?string $maxHeight = '360px';

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('crm::filament/similar-leads.graph.heading');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $lead = Lead::withTrashed()->find($this->leadId);

        abort_unless($lead && LeadResourceResolver::resourceFor($lead)::canView($lead), 403);

        $counts = Lead::query()
            ->withTrashed()
            ->whereKey([$lead->getKey(), ...SimilarLeads::visibleRecords($lead)->modelKeys()])
            ->selectRaw('stage_id, count(*) as aggregate')
            ->groupBy('stage_id')
            ->pluck('aggregate', 'stage_id');

        $stages = Stage::query()->whereKey($counts->keys())->orderBy('sort')->pluck('name', 'id');

        return [
            'datasets' => [
                $this->barDataset(__('crm::filament/similar-leads.graph.dataset'), $stages->keys()->map(fn (int $id): int => (int) $counts[$id])->all()),
            ],
            'labels' => $stages->values()->all(),
        ];
    }

    protected function getOptions(): array
    {
        return $this->barOptions();
    }
}
