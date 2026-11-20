<?php

namespace Webkul\Crm\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Models\Lead;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class TeamDashboard
{
    public const WEEKS = 5;

    public function forTeams(array $teamIds): array
    {
        $teamIds = array_values(array_unique(array_filter($teamIds)));

        if ($teamIds === []) {
            return [];
        }

        $weeks = $this->weeks();
        $monthStart = CarbonImmutable::today()->startOfMonth();

        $ordersToInvoice = Order::query()
            ->whereIn('team_id', $teamIds)
            ->where('invoice_status', InvoiceStatus::TO_INVOICE)
            ->selectRaw('team_id, count(*) as order_count, sum(amount_untaxed) as order_amount')
            ->groupBy('team_id')
            ->get()
            ->keyBy('team_id');

        $quotations = Order::query()
            ->whereIn('team_id', $teamIds)
            ->whereIn('state', [OrderState::DRAFT, OrderState::SENT])
            ->selectRaw('team_id, count(*) as quotation_count, sum(amount_untaxed) as quotation_amount')
            ->groupBy('team_id')
            ->get()
            ->keyBy('team_id');

        $invoices = Move::query()
            ->join('sales_order_invoices', 'sales_order_invoices.move_id', '=', 'accounts_account_moves.id')
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_invoices.order_id')
            ->whereIn('sales_orders.team_id', $teamIds)
            ->where('accounts_account_moves.state', MoveState::POSTED)
            ->whereIn('accounts_account_moves.move_type', [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])
            ->whereDate('accounts_account_moves.invoice_date', '>=', $monthStart)
            ->distinct()
            ->get([
                'sales_orders.team_id as dashboard_team_id',
                'accounts_account_moves.id as dashboard_move_id',
                'accounts_account_moves.invoice_date as dashboard_invoice_date',
                'accounts_account_moves.amount_untaxed_signed as dashboard_amount',
            ])
            ->groupBy('dashboard_team_id');

        $opportunities = Lead::query()
            ->withTrashed()
            ->whereIn('team_id', $teamIds)
            ->where('type', LeadType::OPPORTUNITY)
            ->where('created_at', '>=', $weeks->first()['start'])
            ->get(['team_id', 'created_at'])
            ->groupBy('team_id');

        return collect($teamIds)
            ->mapWithKeys(function (int $teamId) use ($weeks, $ordersToInvoice, $quotations, $invoices, $opportunities): array {
                $created = ($opportunities->get($teamId) ?? collect())->pluck('created_at');

                return [$teamId => [
                    'quotations'               => (int) ($quotations->get($teamId)?->quotation_count ?? 0),
                    'quotations_amount'        => (float) ($quotations->get($teamId)?->quotation_amount ?? 0),
                    'orders_to_invoice'        => (int) ($ordersToInvoice->get($teamId)?->order_count ?? 0),
                    'orders_to_invoice_amount' => (float) ($ordersToInvoice->get($teamId)?->order_amount ?? 0),
                    'invoiced_month'           => (float) ($invoices->get($teamId)?->sum('dashboard_amount') ?? 0),
                    'weeks'                    => $weeks->map(fn (array $week): array => [
                        'label' => $week['label'],
                        'count' => $created->filter(fn ($date): bool => $date->between($week['start'], $week['end']->endOfDay()))->count(),
                    ])->all(),
                ]];
            })
            ->all();
    }

    protected function weeks(): Collection
    {
        $currentWeek = CarbonImmutable::today()->startOfWeek();

        return collect(range(static::WEEKS - 1, 0))
            ->map(function (int $weeksAgo) use ($currentWeek): array {
                $start = $currentWeek->subWeeks($weeksAgo);
                $end = $start->endOfWeek();

                return [
                    'start' => $start,
                    'end'   => $end,
                    'label' => $start->month === $end->month
                        ? $start->format('j').'–'.$end->format('j M')
                        : $start->format('j M').'–'.$end->format('j M'),
                ];
            });
    }
}
