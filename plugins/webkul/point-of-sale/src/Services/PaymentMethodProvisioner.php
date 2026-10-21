<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Database\Eloquent\Collection;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Journal;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\PaymentMethod;

class PaymentMethodProvisioner
{
    public function ensureFor(Config $config): void
    {
        if ($config->paymentMethods()->exists()) {
            return;
        }

        $methods = $this->defaultsFor($config);

        if (! $methods->contains(fn (PaymentMethod $method): bool => $method->is_cash_count)) {
            $cash = $this->cashMethodFor($config) ?? $this->provisionCashMethod($config);

            if ($cash) {
                $methods->push($cash);
            }
        }

        if ($methods->isEmpty()) {
            return;
        }

        $config->paymentMethods()->syncWithoutDetaching($methods->pluck('id')->all());
    }

    public function defaultsFor(Config $config): Collection
    {
        return PaymentMethod::query()
            ->where(owned_by_company($config->company_id))
            ->where('is_cash_count', false)
            ->where('is_split_transaction', false)
            ->get();
    }

    public function cashMethodFor(Config $config): ?PaymentMethod
    {
        return PaymentMethod::query()
            ->where(owned_by_company($config->company_id))
            ->where('is_cash_count', true)
            ->whereDoesntHave('configs')
            ->orderBy('id')
            ->first();
    }

    public function provisionCashMethod(Config $config): ?PaymentMethod
    {
        $journal = $this->cashJournalFor($config);

        if (! $journal) {
            return null;
        }

        return PaymentMethod::create([
            'name'       => __('point-of-sale::system.payment-method-provisioner.cash'),
            'journal_id' => $journal->id,
            'company_id' => $config->company_id,
        ]);
    }

    protected function cashJournalFor(Config $config): ?Journal
    {
        $journal = Journal::query()
            ->where('type', JournalType::CASH)
            ->where(owned_by_company($config->company_id))
            ->orderBy('id')
            ->first();

        if ($journal || ! $config->company_id) {
            return $journal;
        }

        return Journal::create([
            'name'       => __('point-of-sale::system.payment-method-provisioner.cash-journal'),
            'type'       => JournalType::CASH,
            'company_id' => $config->company_id,
        ]);
    }
}
