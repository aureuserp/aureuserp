<?php

namespace Webkul\Crm\Filament\Support;

use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IsLostOperator extends Operator
{
    public function getName(): string
    {
        return 'isLost';
    }

    public function getLabel(): string
    {
        return $this->isInverse()
            ? __('crm::filament/clusters/reporting/report.rules.lost.is-not-lost')
            : __('crm::filament/clusters/reporting/report.rules.lost.is-lost');
    }

    public function getSummary(): string
    {
        return $this->isInverse()
            ? __('crm::filament/clusters/reporting/report.rules.lost.not-lost')
            : __('crm::filament/clusters/reporting/report.rules.lost.label');
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $this->isInverse() ? $query->whereNull($qualifiedColumn) : $query->whereNotNull($qualifiedColumn);
    }

    public function applyToBaseFilterQuery(Builder $query): Builder
    {
        return $query->withoutGlobalScope(SoftDeletingScope::class);
    }
}
