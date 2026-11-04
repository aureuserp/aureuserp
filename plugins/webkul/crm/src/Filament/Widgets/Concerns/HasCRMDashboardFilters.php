<?php

namespace Webkul\Crm\Filament\Widgets\Concerns;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Models\Lead;

trait HasCRMDashboardFilters
{
    use InteractsWithPageFilters;

    protected function crmRecords(): Builder
    {
        $filters = $this->pageFilters ?? [];

        return Lead::query()
            ->withTrashed()
            ->when(! empty($filters['teams']), fn (Builder $query): Builder => $query->whereIn('crm_leads.team_id', $filters['teams']))
            ->when(! empty($filters['salesPersons']), fn (Builder $query): Builder => $query->whereIn('crm_leads.user_id', $filters['salesPersons']));
    }

    protected function createdLeads(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->createdBetween($this->crmRecords()->leads(), $start, $end);
    }

    protected function createdOpportunities(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->createdBetween($this->crmRecords()->opportunities(), $start, $end);
    }

    protected function wonOpportunities(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->closedBetween($this->crmRecords()->opportunities()->withoutTrashed()->won(), $start, $end);
    }

    protected function lostOpportunities(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->closedBetween($this->crmRecords()->opportunities()->onlyTrashed(), $start, $end);
    }

    protected function openOpportunities(): Builder
    {
        return $this->crmRecords()->opportunities()->withoutTrashed()->open();
    }

    protected function createdBetween(Builder $query, ?Carbon $start, ?Carbon $end): Builder
    {
        [$defaultStart, $defaultEnd] = $this->periodRange();

        return $query->whereBetween('crm_leads.created_at', [$start ?? $defaultStart, $end ?? $defaultEnd]);
    }

    protected function closedBetween(Builder $query, ?Carbon $start, ?Carbon $end): Builder
    {
        [$defaultStart, $defaultEnd] = $this->periodRange();

        return $query->whereBetween('crm_leads.closed_at', [$start ?? $defaultStart, $end ?? $defaultEnd]);
    }

    protected function periodRange(): array
    {
        $filters = $this->pageFilters ?? [];

        return [
            filled($filters['startDate'] ?? null) ? Carbon::parse($filters['startDate'])->startOfDay() : now()->startOfYear(),
            filled($filters['endDate'] ?? null) ? Carbon::parse($filters['endDate'])->endOfDay() : now()->endOfYear(),
        ];
    }

    protected function previousPeriodRange(): array
    {
        [$start, $end] = $this->periodRange();

        $length = $start->diffInDays($end) + 1;

        return [$start->copy()->subDays($length), $end->copy()->subDays($length)];
    }

    protected function currencyCode(): string
    {
        return Report::getCurrencyCode();
    }
}
