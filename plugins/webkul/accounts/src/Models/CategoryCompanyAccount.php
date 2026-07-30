<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class CategoryCompanyAccount extends Model
{
    use BelongsToCompany;
    use ChecksCompanyConsistency;

    protected $table = 'products_category_company_accounts';

    protected $fillable = [
        'category_id',
        'company_id',
        'property_account_income_id',
        'property_account_expense_id',
        'property_account_down_payment_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
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

    public function propertyAccountDownPayment(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'property_account_down_payment_id');
    }

    public function companyConsistentFields(): array
    {
        return [
            'property_account_income_id'       => Account::class,
            'property_account_expense_id'      => Account::class,
            'property_account_down_payment_id' => Account::class,
        ];
    }
}
