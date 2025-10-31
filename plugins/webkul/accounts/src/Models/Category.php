<?php

namespace Webkul\Account\Models;

use Webkul\Account\Enums\AccountType;
use Webkul\Product\Models\Category as BaseCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends BaseCategory
{
    /**
     * Create a new Eloquent model instance.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'property_account_income_id',
            'property_account_expense_id',
            'property_account_down_payment_id',
        ]);

        parent::__construct($attributes);
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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
