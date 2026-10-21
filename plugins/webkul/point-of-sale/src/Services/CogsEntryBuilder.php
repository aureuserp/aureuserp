<?php

namespace Webkul\PointOfSale\Services;

use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Product as AccountProduct;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Session;
use Webkul\Product\Enums\ProductType;

class CogsEntryBuilder
{
    public function __construct(
        protected AccountResolver $accounts,
    ) {}

    public function post(Session $session): ?Move
    {
        if (! $session->config->enable_cogs) {
            return null;
        }

        $buckets = $this->accumulate($session);

        if (empty($buckets)) {
            return null;
        }

        $company = $session->config->company;

        $currency = $session->currency ?? $company->currency;

        $stockOutput = $this->accounts->stockOutputAccountFor($session->config);

        if (! $stockOutput) {
            throw new PosConfigurationException(
                __('point-of-sale::system.session-closer.stock-output-missing')
            );
        }

        $move = Move::create([
            'move_type'   => MoveType::ENTRY,
            'state'       => MoveState::DRAFT,
            'journal_id'  => $session->config->cogs_journal_id ?? $session->config->journal_id,
            'company_id'  => $company->id,
            'currency_id' => $currency->id,
            'date'        => $session->stopped_at ?? now(),
            'reference'   => __('point-of-sale::system.session-closer.cogs-reference', ['session' => $session->name]),
        ]);

        $total = 0.0;

        foreach ($buckets as $accountId => $amount) {
            if (float_is_zero($amount, precisionDigits: 4)) {
                continue;
            }

            $total += $amount;

            MoveLine::create([
                'move_id'             => $move->id,
                'account_id'          => $accountId,
                'name'                => __('point-of-sale::system.session-closer.cogs-line'),
                'display_type'        => DisplayType::COGS,
                'debit'               => $amount > 0 ? $amount : 0.0,
                'credit'              => $amount < 0 ? abs($amount) : 0.0,
                'balance'             => $amount,
                'amount_currency'     => $amount,
                'currency_id'         => $currency->id,
                'company_currency_id' => $company->currency_id,
                'company_id'          => $company->id,
                'date'                => $move->date,
            ]);
        }

        MoveLine::create([
            'move_id'             => $move->id,
            'account_id'          => $stockOutput->id,
            'name'                => __('point-of-sale::system.session-closer.stock-output-line'),
            'debit'               => $total < 0 ? abs($total) : 0.0,
            'credit'              => $total > 0 ? $total : 0.0,
            'balance'             => -$total,
            'amount_currency'     => -$total,
            'currency_id'         => $currency->id,
            'company_currency_id' => $company->currency_id,
            'company_id'          => $company->id,
            'date'                => $move->date,
        ]);

        AccountFacade::computeAccountMove($move);

        AccountFacade::confirmMove($move->refresh());

        return $move->refresh();
    }

    protected function accumulate(Session $session): array
    {
        $buckets = [];

        $orders = Order::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->whereIn('state', [OrderState::PAID, OrderState::DONE, OrderState::INVOICED])
            ->whereNull('shipped_at')
            ->with('lines.product')
            ->get();

        foreach ($orders as $order) {
            foreach ($order->lines as $line) {
                if (! $this->movesStock($line)) {
                    continue;
                }

                $amount = (float) $line->total_cost;

                if (float_is_zero($amount, precisionDigits: 4)) {
                    continue;
                }

                $account = $this->expenseAccountFor($session, $line);

                $buckets[$account->id] ??= 0.0;

                $buckets[$account->id] += $amount;
            }
        }

        return $buckets;
    }

    protected function movesStock(OrderLine $line): bool
    {
        return $line->product?->type === ProductType::GOODS
            && $line->product?->is_storable;
    }

    protected function expenseAccountFor(Session $session, OrderLine $line)
    {
        $product = AccountProduct::withoutGlobalScopes()->find($line->product_id);

        $account = $this->accounts->expenseAccountFor($session->config, $product);

        if (! $account) {
            throw new PosConfigurationException(
                __('point-of-sale::system.session-closer.expense-account-missing', [
                    'product' => $line->full_product_name ?? $line->product?->name,
                ])
            );
        }

        return $account;
    }
}
