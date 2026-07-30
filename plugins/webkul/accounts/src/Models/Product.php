<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Account\Database\Factories\ProductFactory;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Product\Models\Product as BaseProduct;

class Product extends BaseProduct
{
    use HasChatter, HasCustomFields, HasLogActivity;

    public const ACTIVITY_PLAN_PLUGIN = 'accounts';

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'property_account_income_id',
            'property_account_expense_id',
            'image',
            'service_type',
            'sale_line_warn',
            'expense_policy',
            'invoice_policy',
            'sale_line_warn_msg',
            'sales_ok',
            'purchase_ok',
        ]);

        parent::__construct($attributes);
    }

    protected array $logAttributes = [
        'type',
        'name',
        'service_tracking',
        'reference',
        'barcode',
        'price',
        'cost',
        'volume',
        'weight',
        'description',
        'description_purchase',
        'description_sale',
        'enable_sales',
        'enable_purchase',
        'is_favorite',
        'is_configurable',
        'parent.name'   => 'Parent',
        'category.name' => 'Category',
        'company.name'  => 'Company',
        'creator.name'  => 'Creator',
    ];

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

    protected $appends = [
        'property_account_income_id',
        'property_account_expense_id',
    ];

    protected array $pendingCompanyAccounts = [];

    protected static function booted(): void
    {
        static::saved(function (self $product): void {
            $product->flushPendingCompanyAccounts();
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

        $existing = ProductCompanyAccount::query()
            ->where('product_id', $this->id)
            ->where('company_id', $companyId)
            ->first();

        if (! $existing && ! array_filter($pending, fn ($value) => filled($value))) {
            return;
        }

        ProductCompanyAccount::updateOrCreate(
            ['product_id' => $this->id, 'company_id' => $companyId],
            $pending,
        );

        $this->unsetRelation('companyAccounts');
    }

    public function companyAccounts(): HasMany
    {
        return $this->hasMany(ProductCompanyAccount::class, 'product_id');
    }

    public function companyAccountsFor(): ?ProductCompanyAccount
    {
        $companyId = $this->accountCompanyId();

        if (! $companyId || ! $this->exists) {
            return null;
        }

        return $this->companyAccounts->firstWhere('company_id', $companyId);
    }

    public function getAccounts(): array
    {
        $own = $this->companyAccountsFor();

        $parent = $this->parent_id ? $this->parent?->companyAccountsFor() : null;

        $settings = settings(DefaultAccountSettings::class);

        return [
            'income' => $own?->propertyAccountIncome
                ?? $parent?->propertyAccountIncome
                ?? $this->category?->propertyAccountIncome
                ?? Account::find($settings->income_account_id),
            'expense' => $own?->propertyAccountExpense
                ?? $parent?->propertyAccountExpense
                ?? $this->category?->propertyAccountExpense
                ?? Account::find($settings->expense_account_id),
        ];
    }

    public function getAccountsFromFiscalPosition($fiscalPosition = null)
    {
        $accounts = $this->getAccounts();

        $fiscalPosition = $fiscalPosition ?? new FiscalPosition;

        $result = [];

        foreach ($accounts as $key => $account) {
            $result[$key] = $fiscalPosition->mapAccount($account);
        }

        return $result;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productTaxes()
    {
        return $this->belongsToMany(Tax::class, 'accounts_product_taxes', 'product_id', 'tax_id');
    }

    public function supplierTaxes()
    {
        return $this->belongsToMany(Tax::class, 'accounts_product_supplier_taxes', 'product_id', 'tax_id');
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
