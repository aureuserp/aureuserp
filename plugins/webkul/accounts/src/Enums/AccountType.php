<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum AccountType: string implements HasLabel
{
    case ASSET_RECEIVABLE = 'asset_receivable';
    case ASSET_CASH = 'asset_cash';
    case ASSET_CURRENT = 'asset_current';
    case ASSET_NON_CURRENT = 'asset_non_current';
    case ASSET_PREPAYMENTS = 'asset_prepayments';
    case ASSET_FIXED = 'asset_fixed';
    case LIABILITY_PAYABLE = 'liability_payable';
    case LIABILITY_CREDIT_CARD = 'liability_credit_card';
    case LIABILITY_CURRENT = 'liability_current';
    case LIABILITY_NON_CURRENT = 'liability_non_current';
    case EQUITY = 'equity';
    case EQUITY_UNAFFECTED = 'equity_unaffected';
    case INCOME = 'income';
    case INCOME_OTHER = 'income_other';
    case EXPENSE = 'expense';
    case EXPENSE_DEPRECIATION = 'expense_depreciation';
    case EXPENSE_DIRECT_COST = 'expense_direct_cost';
    case OFF_BALANCE = 'off_balance';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ASSET_RECEIVABLE      => __('accounts::enums/account-type.assets.options.receivable'),
            self::ASSET_CASH            => __('accounts::enums/account-type.assets.options.cash'),
            self::ASSET_CURRENT         => __('accounts::enums/account-type.assets.options.current'),
            self::ASSET_NON_CURRENT     => __('accounts::enums/account-type.assets.options.non-current'),
            self::ASSET_PREPAYMENTS     => __('accounts::enums/account-type.assets.options.prepayments'),
            self::ASSET_FIXED           => __('accounts::enums/account-type.assets.options.fixed'),
            self::LIABILITY_PAYABLE     => __('accounts::enums/account-type.liabilities.options.payable'),
            self::LIABILITY_CREDIT_CARD => __('accounts::enums/account-type.liabilities.options.credit-card'),
            self::LIABILITY_CURRENT     => __('accounts::enums/account-type.liabilities.options.current'),
            self::LIABILITY_NON_CURRENT => __('accounts::enums/account-type.liabilities.options.non-current'),
            self::EQUITY                => __('accounts::enums/account-type.equity.options.equity'),
            self::EQUITY_UNAFFECTED     => __('accounts::enums/account-type.equity.options.unaffected'),
            self::INCOME                => __('accounts::enums/account-type.income.options.income'),
            self::INCOME_OTHER          => __('accounts::enums/account-type.income.options.other'),
            self::EXPENSE               => __('accounts::enums/account-type.expenses.options.expense'),
            self::EXPENSE_DEPRECIATION  => __('accounts::enums/account-type.expenses.options.depreciation'),
            self::EXPENSE_DIRECT_COST   => __('accounts::enums/account-type.expenses.options.direct-cost'),
            self::OFF_BALANCE           => __('accounts::enums/account-type.off-balance.options.off-balance'),
        };
    }

    public static function assets(): array
    {
        return [
            self::ASSET_RECEIVABLE->value  => self::ASSET_RECEIVABLE->getLabel(),
            self::ASSET_CASH->value        => self::ASSET_CASH->getLabel(),
            self::ASSET_CURRENT->value     => self::ASSET_CURRENT->getLabel(),
            self::ASSET_NON_CURRENT->value => self::ASSET_NON_CURRENT->getLabel(),
            self::ASSET_PREPAYMENTS->value => self::ASSET_PREPAYMENTS->getLabel(),
            self::ASSET_FIXED->value       => self::ASSET_FIXED->getLabel(),
        ];
    }

    public static function liabilities(): array
    {
        return [
            self::LIABILITY_PAYABLE->value     => self::LIABILITY_PAYABLE->getLabel(),
            self::LIABILITY_CREDIT_CARD->value => self::LIABILITY_CREDIT_CARD->getLabel(),
            self::LIABILITY_CURRENT->value     => self::LIABILITY_CURRENT->getLabel(),
            self::LIABILITY_NON_CURRENT->value => self::LIABILITY_NON_CURRENT->getLabel(),
        ];
    }

    public static function equity(): array
    {
        return [
            self::EQUITY->value            => self::EQUITY->getLabel(),
            self::EQUITY_UNAFFECTED->value => self::EQUITY_UNAFFECTED->getLabel(),
        ];
    }

    public static function income(): array
    {
        return [
            self::INCOME->value       => self::INCOME->getLabel(),
            self::INCOME_OTHER->value => self::INCOME_OTHER->getLabel(),
        ];
    }

    public static function expenses(): array
    {
        return [
            self::EXPENSE->value              => self::EXPENSE->getLabel(),
            self::EXPENSE_DEPRECIATION->value => self::EXPENSE_DEPRECIATION->getLabel(),
            self::EXPENSE_DIRECT_COST->value  => self::EXPENSE_DIRECT_COST->getLabel(),
        ];
    }

    public static function offBalance(): array
    {
        return [
            self::OFF_BALANCE->value => self::OFF_BALANCE->getLabel(),
        ];
    }

    public static function groupedOptions(): array
    {
        return [
            __('accounts::enums/account-type.assets.label')      => self::assets(),
            __('accounts::enums/account-type.liabilities.label') => self::liabilities(),
            __('accounts::enums/account-type.equity.label')      => self::equity(),
            __('accounts::enums/account-type.income.label')      => self::income(),
            __('accounts::enums/account-type.expenses.label')    => self::expenses(),
            __('accounts::enums/account-type.off-balance.label') => self::offBalance(),
        ];
    }
}
