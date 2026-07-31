<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Account\Casts\CompanyProperty;
use Webkul\Account\Database\Factories\ProductFactory;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Product\Models\Product as BaseProduct;
use Webkul\Support\Models\Scopes\CompaniesScope;

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

    public function companyAccountValue(string $field, ?int $companyId = null): mixed
    {
        return CompanyProperty::valueFor($this, ProductCompanyAccount::class, 'product_id', $field, $companyId);
    }

    public function getAccounts(?int $companyId = null): array
    {
        $settings = settings(DefaultAccountSettings::class);

        return [
            'income'  => $this->resolveAccount('property_account_income_id', $settings->income_account_id, $companyId),
            'expense' => $this->resolveAccount('property_account_expense_id', $settings->expense_account_id, $companyId),
        ];
    }

    protected function resolveAccount(string $field, mixed $fallbackId, ?int $companyId): ?Account
    {
        $accountId = $this->companyAccountValue($field, $companyId)
            ?: ($this->parent_id ? $this->parent?->companyAccountValue($field, $companyId) : null)
            ?: $this->category?->accountFromHierarchy($field, $companyId)
            ?: Account::resolveForCompany($fallbackId, $companyId);

        return $accountId
            ? Account::withoutGlobalScope(CompaniesScope::class)->find($accountId)
            : null;
    }

    public function getAccountsFromFiscalPosition($fiscalPosition = null, ?int $companyId = null)
    {
        $accounts = $this->getAccounts($companyId);

        $fiscalPosition = $fiscalPosition ?? new FiscalPosition;

        $result = [];

        foreach ($accounts as $key => $account) {
            $result[$key] = $account ? $fiscalPosition->mapAccount($account) : null;
        }

        return $result;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
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
