<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class ProductCompanyAccount extends Model
{
    use BelongsToCompany;
    use ChecksCompanyConsistency;

    protected $table = 'products_product_company_accounts';

    protected $fillable = [
        'product_id',
        'company_id',
        'property_account_income_id',
        'property_account_expense_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function propertyAccountIncome(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'property_account_income_id');
    }

    public function propertyAccountExpense(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'property_account_expense_id');
    }

    public function companyConsistentFields(): array
    {
        return [
            'property_account_income_id'  => Account::class,
            'property_account_expense_id' => Account::class,
        ];
    }
}
