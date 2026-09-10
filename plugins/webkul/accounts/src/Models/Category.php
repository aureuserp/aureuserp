<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Account\Casts\CompanyProperty;
use Webkul\Account\Enums\AccountType;
use Webkul\Product\Models\Category as BaseCategory;

class Category extends BaseCategory
{
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'property_account_income_id',
            'property_account_expense_id',
            'property_account_down_payment_id',
        ]);

        parent::__construct($attributes);
    }

    public function companyAccountValue(string $field, ?int $companyId = null): mixed
    {
        return CompanyProperty::valueFor($this, CategoryCompanyAccount::class, 'category_id', $field, $companyId);
    }

    public function accountFromHierarchy(string $field, ?int $companyId = null): mixed
    {
        $category = $this;

        $seen = [];

        while ($category && ! in_array($category->id, $seen, true)) {
            $seen[] = $category->id;

            if ($accountId = $category->companyAccountValue($field, $companyId)) {
                return $accountId;
            }

            $category = $category->parent_id ? $category->parent : null;
        }

        return null;
    }

    public function propertyAccountIncome(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'property_account_income_id')
            ->where('deprecated', false)
            ->whereNotIn('account_type', [
                AccountType::ASSET_RECEIVABLE,
                AccountType::LIABILITY_PAYABLE,
                AccountType::ASSET_CASH,
                AccountType::LIABILITY_CREDIT_CARD,
                AccountType::OFF_BALANCE,
            ]);
    }

    public function propertyAccountExpense(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'property_account_expense_id')
            ->where('deprecated', false)
            ->whereNotIn('account_type', [
                AccountType::ASSET_RECEIVABLE,
                AccountType::LIABILITY_PAYABLE,
                AccountType::ASSET_CASH,
                AccountType::LIABILITY_CREDIT_CARD,
                AccountType::OFF_BALANCE,
            ]);
    }

    public function propertyAccountDownPayment(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'property_account_down_payment_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
