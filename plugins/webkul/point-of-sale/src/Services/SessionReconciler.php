<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Collection;
use Throwable;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Payment;
use Webkul\PointOfSale\Models\Session;

class SessionReconciler
{
    public function __construct(
        protected AccountResolver $accounts,
    ) {}

    public function reconcile(Session $session, Move $move): void
    {
        $move->loadMissing('lines.account');

        $reconcilable = $move->lines->filter(fn (MoveLine $line): bool => (bool) $line->account?->reconcile);

        [$invoiceLines, $sessionLines] = $reconcilable->partition(fn (MoveLine $line): bool => $line->partner_id !== null);

        $paymentLines = $this->invoicePaymentReceivableLines($session);

        $sessionLines
            ->groupBy('account_id')
            ->each(fn (Collection $lines, $accountId) => $this->reconcileLines(
                $lines->merge($paymentLines->where('account_id', (int) $accountId)),
            ));

        $this->reconcileInvoices($session, $invoiceLines);
    }

    protected function invoicePaymentReceivableLines(Session $session): Collection
    {
        $account = $this->accounts->receivableAccountFor($session->config);

        if (! $account) {
            return collect();
        }

        $moveIds = Payment::withoutGlobalScopes()
            ->whereIn('order_id', $this->invoicedOrderIds($session))
            ->whereNotNull('account_move_id')
            ->pluck('account_move_id');

        if ($moveIds->isEmpty()) {
            return collect();
        }

        return MoveLine::query()
            ->whereIn('move_id', $moveIds)
            ->where('account_id', $account->id)
            ->whereNull('partner_id')
            ->where('reconciled', false)
            ->get();
    }

    protected function reconcileInvoices(Session $session, Collection $invoiceLines): void
    {
        if ($invoiceLines->isEmpty()) {
            return;
        }

        $invoiceIds = Order::withoutGlobalScopes()
            ->whereIn('id', $this->invoicedOrderIds($session))
            ->whereNotNull('account_move_id')
            ->pluck('account_move_id');

        if ($invoiceIds->isEmpty()) {
            return;
        }

        $invoiceLines
            ->groupBy(fn (MoveLine $line): string => $line->account_id.'|'.$line->partner_id)
            ->each(function (Collection $lines) use ($invoiceIds): void {
                $first = $lines->first();

                $counterparts = MoveLine::query()
                    ->whereIn('move_id', $invoiceIds)
                    ->where('account_id', $first->account_id)
                    ->where('partner_id', $first->partner_id)
                    ->where('reconciled', false)
                    ->get();

                $this->reconcileLines($lines->map->refresh()->reject->reconciled->merge($counterparts));
            });
    }

    protected function invoicedOrderIds(Session $session)
    {
        return Order::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->where('is_invoiced', true)
            ->select('id');
    }

    protected function reconcileLines(Collection $lines): void
    {
        if ($lines->count() < 2) {
            return;
        }

        try {
            AccountFacade::reconcile($lines);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
