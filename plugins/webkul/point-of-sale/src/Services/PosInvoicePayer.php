<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Payment;

class PosInvoicePayer
{
    public function __construct(
        protected AccountResolver $accounts,
    ) {}

    public function pay(Move $invoice, Order $order): void
    {
        $receivableLine = $invoice->lines->first(
            fn (MoveLine $line): bool => (bool) $line->account?->reconcile
        );

        if (! $receivableLine) {
            return;
        }

        $posReceivable = $this->accounts->receivableAccountFor($order->config);

        if (! $posReceivable) {
            throw new PosConfigurationException(
                __('point-of-sale::system.session-preflight.receivable-account.missing')
            );
        }

        $order->payments
            ->groupBy('payment_method_id')
            ->map(fn ($rows): array => [
                'payment' => $rows->firstWhere('is_change', false) ?? $rows->first(),
                'amount'  => float_round((float) $rows->sum('amount'), precisionDigits: 4),
            ])
            ->filter(fn (array $tender): bool => ! float_is_zero($tender['amount'], precisionDigits: 2))
            ->each(function (array $tender) use ($invoice, $order, $receivableLine, $posReceivable): void {
                try {
                    $move = DB::transaction(fn (): Move => $this->paymentMove(
                        $invoice,
                        $order,
                        $tender['payment'],
                        $tender['amount'],
                        $receivableLine,
                        $posReceivable,
                    ));

                    $this->reconcile($move, $receivableLine);
                } catch (Throwable $exception) {
                    report($exception);
                }
            });

        $invoice->refresh()->computePaymentState();

        $invoice->save();
    }

    protected function paymentMove(
        Move $invoice,
        Order $order,
        Payment $payment,
        float $amount,
        MoveLine $receivableLine,
        Account $posReceivable,
    ): Move {
        $currencyId = $order->currency_id ?? $order->company?->currency_id;

        $move = Move::create([
            'move_type'   => MoveType::ENTRY,
            'state'       => MoveState::DRAFT,
            'journal_id'  => $order->config->journal_id,
            'company_id'  => $order->company_id,
            'currency_id' => $currencyId,
            'date'        => $order->ordered_at,
            'reference'   => __('point-of-sale::system.invoice-payer.entry-reference', [
                'order'   => $order->name ?? $order->reference,
                'invoice' => $invoice->name,
                'method'  => $payment->paymentMethod?->name,
            ]),
        ]);

        $name = $move->reference;

        MoveLine::create($this->credit($move, $receivableLine->account_id, $amount, $name, $currencyId) + [
            'partner_id' => $receivableLine->partner_id ?? $order->partner_id,
        ]);

        MoveLine::create($this->debit($move, $posReceivable->id, $amount, $name, $currencyId));

        AccountFacade::computeAccountMove($move);

        AccountFacade::confirmMove($move->refresh());

        $order->payments()
            ->where('payment_method_id', $payment->payment_method_id)
            ->update(['account_move_id' => $move->id]);

        return $move->refresh();
    }

    /**
     * @return array<string, mixed>
     */
    protected function credit(Move $move, ?int $accountId, float $amount, string $name, ?int $currencyId): array
    {
        return $this->line($move, $accountId, $amount < 0 ? abs($amount) : 0.0, $amount > 0 ? $amount : 0.0, $name, $currencyId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function debit(Move $move, ?int $accountId, float $amount, string $name, ?int $currencyId): array
    {
        return $this->line($move, $accountId, $amount > 0 ? $amount : 0.0, $amount < 0 ? abs($amount) : 0.0, $name, $currencyId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function line(Move $move, ?int $accountId, float $debit, float $credit, string $name, ?int $currencyId): array
    {
        $balance = float_round($debit - $credit, precisionDigits: 4);

        return [
            'move_id'             => $move->id,
            'account_id'          => $accountId,
            'name'                => $name,
            'date'                => $move->date,
            'company_id'          => $move->company_id,
            'debit'               => float_round($debit, precisionDigits: 4),
            'credit'              => float_round($credit, precisionDigits: 4),
            'balance'             => $balance,
            'amount_currency'     => $balance,
            'currency_id'         => $currencyId,
            'company_currency_id' => $move->company?->currency_id,
        ];
    }

    protected function reconcile(Move $move, MoveLine $receivableLine): void
    {
        $paymentLines = $move->lines
            ->where('account_id', $receivableLine->account_id)
            ->whereNotNull('partner_id');

        if ($paymentLines->isEmpty()) {
            return;
        }

        try {
            AccountFacade::reconcile($paymentLines->push($receivableLine));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
