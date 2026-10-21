<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Collection;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\MoveLine;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Payment;
use Webkul\PointOfSale\Models\Session;

class ClosingEntryBuilder
{
    public function __construct(
        protected AccountResolver $accounts,
        protected FiscalPositionResolver $fiscalPositions,
    ) {}

    public function build(Session $session): array
    {
        $session->loadMissing(['config.company.currency', 'cashMovements']);

        $company = $session->config->company;

        $currency = $session->currency ?? $company->currency;

        $orders = Order::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->whereIn('state', [OrderState::PAID, OrderState::DONE, OrderState::INVOICED])
            ->with(['lines.product', 'lines.taxes', 'payments.paymentMethod', 'partner', 'fiscalPosition', 'accountMove.lines.account'])
            ->get();

        $baseLines = $this->buildBaseLines($session, $orders, $currency, $company);

        $lines = collect();

        if (! empty($baseLines)) {
            $baseLines = Tax::addTaxDetailsToAll($baseLines, $company);
            $baseLines = Tax::roundTaxDetails($baseLines, $company, []);
            $baseLines = Tax::addAccountingDataToAll($baseLines, $company);

            $changes = Tax::buildTaxLineChanges($baseLines, $company, []);

            $lines = $lines
                ->merge($this->salesLines($changes['base_lines_to_update'], $currency, $company, (bool) $session->config->is_closing_entry_by_product))
                ->merge($this->taxLines($changes['tax_lines_to_add'], $currency, $company));
        }

        $lines = $lines
            ->merge($this->roundingLines($session, $orders, $currency, $company))
            ->merge($this->receivableLines($session, $orders, $currency, $company))
            ->merge($this->invoiceReceivableLines($session, $orders, $currency, $company))
            ->merge($this->cashLines($session, $orders, $currency, $company))
            ->merge($this->cashDifferenceLines($session, $currency, $company));

        return $lines->values()->all();
    }

    public function balanceDelta(array $lines): float
    {
        $debit = array_sum(array_map(fn (array $line): float => (float) ($line['debit'] ?? 0), $lines));

        $credit = array_sum(array_map(fn (array $line): float => (float) ($line['credit'] ?? 0), $lines));

        return float_round($debit - $credit, precisionDigits: 6);
    }

    public function balancingLine(Account $account, float $delta, $currency, $company): array
    {
        return $this->line(
            $account->id,
            $delta < 0 ? abs($delta) : 0.0,
            $delta > 0 ? $delta : 0.0,
            __('point-of-sale::system.session-closer.balancing-line'),
            $currency,
            $company,
        );
    }

    protected function buildBaseLines(Session $session, Collection $orders, $currency, $company): array
    {
        $baseLines = [];

        foreach ($orders as $order) {
            if ($order->is_invoiced) {
                continue;
            }

            foreach ($order->lines as $line) {
                if (float_is_zero((float) $line->qty, precisionDigits: 4)) {
                    continue;
                }

                $baseLines[] = $this->baseLine($session, $order, $line, $currency, $company);
            }
        }

        return $baseLines;
    }

    protected function baseLine(Session $session, Order $order, OrderLine $line, $currency, $company): array
    {
        $account = $this->accounts->incomeAccountFor($session->config, $line->product);

        if (! $account) {
            throw new PosConfigurationException(
                __('point-of-sale::system.session-closer.income-account-missing', [
                    'product' => $line->full_product_name ?? $line->product?->name,
                ])
            );
        }

        $fiscalPosition = $order->fiscalPosition;

        $account = $this->fiscalPositions->mapAccount($fiscalPosition, $account);

        return Tax::makeBaseLine(
            record: $line,
            product: $line->product,
            taxes: $this->fiscalPositions->mapTaxes($fiscalPosition, $line->taxes),
            priceUnit: (float) $line->price_unit,
            quantity: (float) $line->qty,
            discount: (float) $line->discount,
            currency: $currency,
            sign: -1.0,
            isRefund: float_compare((float) $line->qty * (float) $line->price_unit, 0, precisionDigits: 4) < 0,
            partner: $order->partner,
            account: $account,
        );
    }

    protected function salesLines(array $baseLines, $currency, $company, bool $byProduct = false): Collection
    {
        $grouped = [];

        foreach ($baseLines as $baseLine) {
            $account = $baseLine['account'] ?? null;

            $accountId = is_object($account) ? $account->id : $account;

            $balance = (float) ($baseLine['balance'] ?? 0);

            $taxIds = collect($baseLine['taxes'] ?? [])
                ->map(fn ($tax) => is_object($tax) ? $tax->id : $tax)
                ->sort()
                ->implode(',');

            $product = $byProduct ? ($baseLine['product'] ?? null) : null;

            $productId = is_object($product) ? $product->id : null;

            $key = $accountId.'|'.($balance < 0 ? '-' : '+').'|'.$taxIds.'|'.($productId ?? '');

            $grouped[$key] ??= [
                'account_id' => $accountId,
                'balance'    => 0.0,
                'quantity'   => $byProduct ? 0.0 : null,
                'name'       => $productId
                    ? ($product->name ?? __('point-of-sale::system.session-closer.sales-line'))
                    : __('point-of-sale::system.session-closer.sales-line'),
            ];

            $grouped[$key]['balance'] += $balance;

            if ($byProduct) {
                $grouped[$key]['quantity'] += (float) ($baseLine['quantity'] ?? 0);
            }
        }

        return collect($grouped)
            ->filter(fn (array $group): bool => ! float_is_zero($group['balance'], precisionDigits: 4))
            ->map(fn (array $group): array => $this->line(
                $group['account_id'],
                $group['balance'] > 0 ? $group['balance'] : 0.0,
                $group['balance'] < 0 ? abs($group['balance']) : 0.0,
                $group['name'],
                $currency,
                $company,
                $group['quantity'],
            ))
            ->values();
    }

    protected function taxLines(array $taxLines, $currency, $company): Collection
    {
        return collect($taxLines)
            ->filter(fn (array $taxLine): bool => ! float_is_zero((float) ($taxLine['balance'] ?? 0), precisionDigits: 4))
            ->map(function (array $taxLine) use ($currency, $company): array {
                $balance = (float) $taxLine['balance'];

                return array_merge(
                    $this->line(
                        $taxLine['account_id'] ?? null,
                        $balance > 0 ? $balance : 0.0,
                        $balance < 0 ? abs($balance) : 0.0,
                        $taxLine['name'] ?? __('point-of-sale::system.session-closer.tax-line'),
                        $currency,
                        $company,
                    ),
                    [
                        'display_type'            => DisplayType::TAX,
                        'tax_line_id'             => $taxLine['tax_line_id'] ?? null,
                        'tax_group_id'            => $taxLine['tax_group_id'] ?? null,
                        'tax_repartition_line_id' => $taxLine['tax_repartition_line_id'] ?? null,
                        'tax_base_amount'         => $taxLine['tax_base_amount'] ?? 0,
                    ],
                );
            })
            ->values();
    }

    protected function roundingLines(Session $session, Collection $orders, $currency, $company): Collection
    {
        $rounding = (float) $orders
            ->reject(fn (Order $order): bool => (bool) $order->is_invoiced)
            ->sum('amount_rounding');

        if (float_is_zero($rounding, precisionDigits: 4)) {
            return collect();
        }

        $cashRounding = $session->config->cashRounding;

        $account = $this->accounts->resolve(
            $rounding > 0 ? $cashRounding?->profit_account_id : $cashRounding?->loss_account_id,
            $session->company_id,
        );

        if (! $account) {
            throw new PosConfigurationException(
                __('point-of-sale::system.session-closer.rounding-account-missing')
            );
        }

        return collect([
            $this->line(
                $account->id,
                $rounding < 0 ? abs($rounding) : 0.0,
                $rounding > 0 ? $rounding : 0.0,
                __('point-of-sale::system.session-closer.rounding-line'),
                $currency,
                $company,
            ),
        ]);
    }

    protected function receivableLines(Session $session, Collection $orders, $currency, $company): Collection
    {
        $buckets = [];

        foreach ($orders as $order) {
            foreach ($order->payments as $payment) {
                if ($payment->paymentMethod?->is_cash_count) {
                    continue;
                }

                $account = $this->accounts->receivableAccountFor($session->config, $payment->paymentMethod);

                if (! $account) {
                    continue;
                }

                $buckets[$account->id] ??= 0.0;

                $buckets[$account->id] += (float) $payment->amount;
            }
        }

        return collect($buckets)
            ->filter(fn (float $amount): bool => ! float_is_zero($amount, precisionDigits: 4))
            ->map(fn (float $amount, $accountId): array => $this->line(
                (int) $accountId,
                $amount > 0 ? $amount : 0.0,
                $amount < 0 ? abs($amount) : 0.0,
                __('point-of-sale::system.session-closer.receivable-line'),
                $currency,
                $company,
            ))
            ->values();
    }

    protected function invoiceReceivableLines(Session $session, Collection $orders, $currency, $company): Collection
    {
        $posReceivable = $this->accounts->receivableAccountFor($session->config);

        $lines = collect();

        $cleared = 0.0;

        foreach ($orders->filter(fn (Order $order): bool => (bool) $order->is_invoiced) as $order) {
            [$paid, $open] = $order->payments->partition(
                fn (Payment $payment): bool => $payment->account_move_id !== null,
            );

            $cleared += (float) $paid->sum('amount');

            $lines = $lines->merge($this->openInvoiceReceivableLine($order, (float) $open->sum('amount'), $currency, $company));
        }

        if ($posReceivable && ! float_is_zero($cleared, precisionDigits: 4)) {
            $lines->push($this->line(
                $posReceivable->id,
                $cleared < 0 ? abs($cleared) : 0.0,
                $cleared > 0 ? $cleared : 0.0,
                __('point-of-sale::system.session-closer.invoice-receivable-line'),
                $currency,
                $company,
            ));
        }

        return $lines->values();
    }

    protected function openInvoiceReceivableLine(Order $order, float $amount, $currency, $company): Collection
    {
        if (float_is_zero($amount, precisionDigits: 4)) {
            return collect();
        }

        $invoiceLine = $order->accountMove?->lines
            ->first(fn (MoveLine $line): bool => $line->account?->account_type === AccountType::ASSET_RECEIVABLE);

        if (! $invoiceLine) {
            return collect();
        }

        return collect([array_merge($this->line(
            $invoiceLine->account_id,
            $amount < 0 ? abs($amount) : 0.0,
            $amount > 0 ? $amount : 0.0,
            __('point-of-sale::system.session-closer.invoice-receivable-line'),
            $currency,
            $company,
        ), [
            'partner_id' => $invoiceLine->partner_id ?? $order->partner_id,
        ])]);
    }

    protected function cashLines(Session $session, Collection $orders, $currency, $company): Collection
    {
        $cashTotal = 0.0;

        foreach ($orders as $order) {
            $cashTotal += (float) $order->payments
                ->filter(fn (Payment $payment): bool => (bool) $payment->paymentMethod?->is_cash_count)
                ->sum('amount');
        }

        if (float_is_zero($cashTotal, precisionDigits: 4)) {
            return collect();
        }

        $account = $this->accounts->resolve(
            $session->resolveCashJournal()?->default_account_id,
            $session->company_id,
        );

        if (! $account) {
            throw new PosConfigurationException(
                __('point-of-sale::system.session-closer.cash-account-missing')
            );
        }

        return collect([
            $this->line(
                $account->id,
                $cashTotal > 0 ? $cashTotal : 0.0,
                $cashTotal < 0 ? abs($cashTotal) : 0.0,
                __('point-of-sale::system.session-closer.cash-line'),
                $currency,
                $company,
            ),
        ]);
    }

    protected function cashDifferenceLines(Session $session, $currency, $company): Collection
    {
        $difference = (float) ($session->cash_difference ?? 0);

        if (float_is_zero($difference, precisionDigits: 4)) {
            return collect();
        }

        $journal = $session->resolveCashJournal();

        $account = $difference > 0
            ? $this->accounts->cashProfitAccountFor($journal, $session->company_id)
            : $this->accounts->cashLossAccountFor($journal, $session->company_id);

        $liquidity = $this->accounts->resolve($journal?->default_account_id, $session->company_id);

        if (! $account || ! $liquidity) {
            throw new PosConfigurationException(
                __('point-of-sale::system.session-closer.cash-difference-account-missing')
            );
        }

        $label = __('point-of-sale::system.session-closer.cash-difference-line');

        return collect([
            $this->line(
                $liquidity->id,
                $difference > 0 ? $difference : 0.0,
                $difference < 0 ? abs($difference) : 0.0,
                $label,
                $currency,
                $company,
            ),
            $this->line(
                $account->id,
                $difference < 0 ? abs($difference) : 0.0,
                $difference > 0 ? $difference : 0.0,
                $label,
                $currency,
                $company,
            ),
        ]);
    }

    protected function line(?int $accountId, float $debit, float $credit, string $name, $currency, $company, ?float $quantity = null): array
    {
        $balance = float_round($debit - $credit, precisionDigits: 4);

        $line = [
            'account_id'          => $accountId,
            'name'                => $name,
            'debit'               => float_round($debit, precisionDigits: 4),
            'credit'              => float_round($credit, precisionDigits: 4),
            'balance'             => $balance,
            'amount_currency'     => $balance,
            'currency_id'         => $currency?->id,
            'company_currency_id' => $company?->currency_id,
        ];

        if ($quantity !== null) {
            $line['quantity'] = float_round($quantity, precisionDigits: 4);
        }

        return $line;
    }
}
