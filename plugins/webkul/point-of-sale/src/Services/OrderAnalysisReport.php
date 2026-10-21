<?php

namespace Webkul\PointOfSale\Services;

use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Models\Order;

class OrderAnalysisReport
{
    public function build(?string $startDate, ?string $endDate, ?int $configId = null, string $groupBy = 'day'): array
    {
        $query = $this->baseQuery($startDate, $endDate, $configId);

        $rows = (clone $query)
            ->selectRaw($this->groupExpression($groupBy).' as bucket')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('SUM(amount_untaxed) as untaxed')
            ->selectRaw('SUM(amount_tax) as taxes')
            ->selectRaw('SUM(amount_total) as total')
            ->selectRaw('SUM(margin) as margin')
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        return [
            'rows'    => $rows,
            'totals'  => [
                'order_count' => (int) $rows->sum('order_count'),
                'untaxed'     => (float) $rows->sum('untaxed'),
                'taxes'       => (float) $rows->sum('taxes'),
                'total'       => (float) $rows->sum('total'),
                'margin'      => (float) $rows->sum('margin'),
            ],
            'average' => $rows->sum('order_count') > 0
                ? float_round((float) $rows->sum('total') / (int) $rows->sum('order_count'), precisionDigits: 2)
                : 0.0,
        ];
    }

    protected function baseQuery(?string $startDate, ?string $endDate, ?int $configId)
    {
        return Order::query()
            ->whereIn('state', [OrderState::PAID, OrderState::DONE, OrderState::INVOICED])
            ->when($startDate, fn ($query) => $query->whereDate('ordered_at', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('ordered_at', '<=', $endDate))
            ->when($configId, fn ($query) => $query->where('config_id', $configId));
    }

    protected function groupExpression(string $groupBy): string
    {
        return match ($groupBy) {
            'month' => "DATE_FORMAT(ordered_at, '%Y-%m')",
            'week'  => "DATE_FORMAT(ordered_at, '%x-W%v')",
            default => 'DATE(ordered_at)',
        };
    }
}
