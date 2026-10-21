<?php

namespace Webkul\PointOfSale\Services;

use Throwable;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Product as AccountProduct;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\PointOfSale\Settings\AccountSettings;
use Webkul\Support\Models\Scopes\CompanyScope;

class AccountResolver
{
    public function receivableAccountFor(Config $config, ?PaymentMethod $paymentMethod = null): ?Account
    {
        $accountId = $paymentMethod?->receivable_account_id
            ?? $config->receivable_account_id
            ?? $this->settingValue('receivable_account_id');

        return $this->resolve($accountId, $config->company_id);
    }

    public function outstandingAccountFor(PaymentMethod $paymentMethod): ?Account
    {
        $accountId = $paymentMethod->outstanding_account_id
            ?? $paymentMethod->journal?->default_account_id;

        return $this->resolve($accountId, $paymentMethod->company_id);
    }

    public function stockOutputAccountFor(Config $config): ?Account
    {
        $accountId = $config->stock_output_account_id
            ?? $this->settingValue('stock_output_account_id');

        return $this->resolve($accountId, $config->company_id);
    }

    public function balancingAccountFor(Config $config): ?Account
    {
        $accountId = $config->balancing_account_id
            ?? $this->settingValue('balancing_account_id');

        return $this->resolve($accountId, $config->company_id);
    }

    public function cashProfitAccountFor(?Journal $journal, ?int $companyId): ?Account
    {
        return $this->resolve(
            $journal?->profit_account_id ?? $this->defaultSettingValue('income_account_id'),
            $companyId,
        );
    }

    public function cashLossAccountFor(?Journal $journal, ?int $companyId): ?Account
    {
        return $this->resolve(
            $journal?->loss_account_id ?? $this->defaultSettingValue('expense_account_id'),
            $companyId,
        );
    }

    public function cashMovementAccountFor(Config $config): ?Account
    {
        $accountId = $config->cash_movement_account_id
            ?? $this->defaultSettingValue('transfer_account_id');

        return $this->resolve($accountId, $config->company_id);
    }

    public function incomeAccountFor(Config $config, $product): ?Account
    {
        $accounts = $this->accountProduct($product)?->getAccounts($config->company_id) ?? [];

        return $accounts['income']
            ?? $this->resolve($config->journal?->default_account_id, $config->company_id);
    }

    public function expenseAccountFor(Config $config, $product): ?Account
    {
        $accounts = $this->accountProduct($product)?->getAccounts($config->company_id) ?? [];

        return $accounts['expense'] ?? null;
    }

    protected function accountProduct($product): ?AccountProduct
    {
        if (! $product) {
            return null;
        }

        if ($product instanceof AccountProduct) {
            return $product;
        }

        return AccountProduct::withoutGlobalScopes()->find($product->getKey());
    }

    public function resolve(?int $accountId, ?int $companyId): ?Account
    {
        if (! $accountId) {
            return null;
        }

        $resolvedId = Account::resolveForCompany($accountId, $companyId);

        if (! $resolvedId) {
            return $companyId
                ? null
                : Account::withoutGlobalScope(CompanyScope::class)->find($accountId);
        }

        return Account::withoutGlobalScope(CompanyScope::class)->find($resolvedId);
    }

    protected function settingValue(string $key): ?int
    {
        try {
            return settings(AccountSettings::class)->{$key};
        } catch (Throwable) {
            return null;
        }
    }

    protected function defaultSettingValue(string $key): ?int
    {
        try {
            return settings(DefaultAccountSettings::class)->{$key};
        } catch (Throwable) {
            return null;
        }
    }
}
