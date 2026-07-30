<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    protected $appends = [
        'property_account_income_id',
        'property_account_expense_id',
        'property_account_down_payment_id',
    ];

    protected array $pendingCompanyAccounts = [];

    protected static function booted(): void
    {
        static::saved(function (self $category): void {
            $category->flushPendingCompanyAccounts();
        });
    }

    public function getPropertyAccountIncomeIdAttribute(): mixed
    {
        return $this->resolveCompanyAccountAttribute('property_account_income_id');
    }

    public function setPropertyAccountIncomeIdAttribute(mixed $value): void
    {
        $this->pendingCompanyAccounts['property_account_income_id'] = $value;
    }

    public function getPropertyAccountExpenseIdAttribute(): mixed
    {
        return $this->resolveCompanyAccountAttribute('property_account_expense_id');
    }

    public function setPropertyAccountExpenseIdAttribute(mixed $value): void
    {
        $this->pendingCompanyAccounts['property_account_expense_id'] = $value;
    }

    public function getPropertyAccountDownPaymentIdAttribute(): mixed
    {
        return $this->resolveCompanyAccountAttribute('property_account_down_payment_id');
    }

    public function setPropertyAccountDownPaymentIdAttribute(mixed $value): void
    {
        $this->pendingCompanyAccounts['property_account_down_payment_id'] = $value;
    }

    public function accountCompanyId(): ?int
    {
        $companyId = current_company_id() ?: $this->company_id;

        return $companyId ? (int) $companyId : null;
    }

    protected function resolveCompanyAccountAttribute(string $field): mixed
    {
        if (array_key_exists($field, $this->pendingCompanyAccounts)) {
            return $this->pendingCompanyAccounts[$field];
        }

        return $this->companyAccountsFor()?->{$field};
    }

    protected function flushPendingCompanyAccounts(): void
    {
        if (! $this->pendingCompanyAccounts) {
            return;
        }

        $pending = $this->pendingCompanyAccounts;

        $this->pendingCompanyAccounts = [];

        $companyId = $this->accountCompanyId();

        if (! $companyId) {
            return;
        }

        $existing = CategoryCompanyAccount::query()
            ->where('category_id', $this->id)
            ->where('company_id', $companyId)
            ->first();

        if (! $existing && ! array_filter($pending, fn ($value) => filled($value))) {
            return;
        }

        CategoryCompanyAccount::updateOrCreate(
            ['category_id' => $this->id, 'company_id' => $companyId],
            $pending,
        );

        $this->unsetRelation('companyAccounts');
    }

    public function companyAccounts(): HasMany
    {
        return $this->hasMany(CategoryCompanyAccount::class, 'category_id');
    }

    public function companyAccountsFor(): ?CategoryCompanyAccount
    {
        $companyId = $this->accountCompanyId();

        if (! $companyId || ! $this->exists) {
            return null;
        }

        return $this->companyAccounts->firstWhere('company_id', $companyId);
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
