<?php

namespace Webkul\PointOfSale\Services;

use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\PaymentMethodType;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Exceptions\SessionHasDraftOrdersException;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\PointOfSale\Models\Session;

class SessionPreflight
{
    public function __construct(
        protected AccountResolver $accounts,
    ) {}

    public function assertCanOpen(Config $config): void
    {
        $config->loadMissing(['journal', 'invoiceJournal', 'paymentMethods.journal']);

        $this->assertJournalType($config->journal, 'journal', JournalType::GENERAL, JournalType::SALE);

        if ($config->invoice_journal_id) {
            $this->assertJournalType($config->invoiceJournal, 'invoice-journal', JournalType::SALE);
        }

        $this->assertReceivableAccount($config);

        $this->assertPaymentMethods($config);

        $this->assertCashJournal($config);

        $this->assertTaxAccounts($config);

        if ($config->enable_cogs) {
            $this->assertCogsAccounts($config);
        }

        if ($config->enable_global_discount && ! $config->discount_product_id) {
            $this->fail('global-discount.product-missing');
        }
    }

    public function assertCanClose(Session $session): void
    {
        $this->assertCanOpen($session->config);

        $this->assertNoDraftOrders($session);
    }

    protected function assertNoDraftOrders(Session $session): void
    {
        $draftOrders = Order::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->where('state', OrderState::DRAFT)
            ->get();

        if ($draftOrders->isEmpty()) {
            return;
        }

        throw new SessionHasDraftOrdersException(
            __('point-of-sale::system.session-preflight.draft-orders', [
                'orders' => $draftOrders->map(fn (Order $order): string => $order->reference)->implode(', '),
            ])
        );
    }

    protected function assertJournalType(?Journal $journal, string $key, JournalType ...$types): void
    {
        if (! $journal) {
            $this->fail("{$key}.missing");
        }

        if (! in_array($journal->type, $types, true)) {
            $this->fail("{$key}.invalid-type");
        }
    }

    protected function assertReceivableAccount(Config $config): void
    {
        $account = $this->accounts->receivableAccountFor($config);

        if (! $account) {
            $this->fail('receivable-account.missing');
        }

        if ($account->account_type !== AccountType::ASSET_RECEIVABLE) {
            $this->fail('receivable-account.invalid-type');
        }

        if (! $account->reconcile) {
            $this->fail('receivable-account.not-reconcilable');
        }

        if ($account->deprecated) {
            $this->fail('receivable-account.deprecated');
        }
    }

    protected function assertPaymentMethods(Config $config): void
    {
        if ($config->paymentMethods->isEmpty()) {
            $this->fail('payment-methods.missing');
        }

        $config->paymentMethods->each(function (PaymentMethod $paymentMethod) use ($config): void {
            if ($paymentMethod->type === PaymentMethodType::PAY_LATER) {
                $this->fail('payment-methods.pay-later-unsupported', ['method' => $paymentMethod->name]);
            }

            if (! $paymentMethod->journal) {
                $this->fail('payment-methods.journal-missing', ['method' => $paymentMethod->name]);
            }

            if ($paymentMethod->journal->company_id !== $config->company_id) {
                $this->fail('payment-methods.journal-company', ['method' => $paymentMethod->name]);
            }

            if (! $this->accounts->receivableAccountFor($config, $paymentMethod)) {
                $this->fail('payment-methods.receivable-missing', ['method' => $paymentMethod->name]);
            }
        });
    }

    protected function assertCashJournal(Config $config): void
    {
        $cashMethods = $config->paymentMethods->where('is_cash_count', true);

        if ($cashMethods->count() > 1) {
            $this->fail('cash-journal.multiple');
        }

        $cashMethod = $cashMethods->first();

        if (! $cashMethod) {
            return;
        }

        $journal = $cashMethod->journal;

        $profit = $this->accounts->cashProfitAccountFor($journal, $config->company_id);

        $loss = $this->accounts->cashLossAccountFor($journal, $config->company_id);

        if (! $profit || ! $loss) {
            $this->fail('cash-journal.profit-loss-missing', ['journal' => $journal->name]);
        }
    }

    protected function assertTaxAccounts(Config $config): void
    {
        $taxes = Tax::withoutGlobalScopes()
            ->where(function ($query) use ($config) {
                $query
                    ->whereNull('company_id')
                    ->orWhere('company_id', $config->company_id);
            })
            ->with(['invoiceRepartitionLines', 'refundRepartitionLines'])
            ->get();

        $taxes->each(function (Tax $tax): void {
            $missing = $tax->invoiceRepartitionLines
                ->merge($tax->refundRepartitionLines)
                ->filter(fn (TaxPartition $partition): bool => $partition->repartition_type === RepartitionType::TAX
                    && ! $partition->account_id);

            if ($missing->isNotEmpty()) {
                $this->fail('taxes.account-missing', ['tax' => $tax->name]);
            }
        });
    }

    protected function assertCogsAccounts(Config $config): void
    {
        if (! $this->accounts->stockOutputAccountFor($config)) {
            $this->fail('cogs.stock-output-missing');
        }
    }

    protected function fail(string $key, array $replace = []): void
    {
        throw new PosConfigurationException(__("point-of-sale::system.session-preflight.{$key}", $replace));
    }
}
