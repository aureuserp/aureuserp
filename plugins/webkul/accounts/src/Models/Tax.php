<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\Currency;
use Webkul\Partner\Models\Partner;

class Tax extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'accounts_taxes';

    protected $fillable = [
        'sort',
        'company_id',
        'tax_group_id',
        'cash_basis_transition_account_id',
        'country_id',
        'creator_id',
        'type_tax_use',
        'tax_scope',
        'amount_type',
        'price_include_override',
        'tax_exigibility',
        'name',
        'description',
        'invoice_label',
        'invoice_legal_notes',
        'amount',
        'is_active',
        'include_base_amount',
        'is_base_affected',
        'analytic',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function taxGroup()
    {
        return $this->belongsTo(TaxGroup::class, 'tax_group_id');
    }

    public function cashBasisTransitionAccount()
    {
        return $this->belongsTo(Account::class, 'cash_basis_transition_account_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function distributionForInvoice()
    {
        return $this->hasMany(TaxPartition::class, 'tax_id');
    }

    public function distributionForRefund()
    {
        return $this->hasMany(TaxPartition::class, 'tax_id');
    }

    public function getPriceIncludeAttribute()
    {
        return $this->price_include_override == 'tax_included'
            || ($this->company->account_price_include == 'tax_included' && ! $this->price_include_override);
    }


    public function evalTaxAmountFixedAmount($batch, $rawBase, $evaluationContext)
    {
        if ($this->amount_type === 'fixed') {
            return $evaluationContext['quantity'] + $this->amount;
        }
    }

    public function evalTaxAmountPriceIncluded($batch, $rawBase, $evaluationContext)
    {
        if ($this->amount_type === 'percent') {
            $totalPercentage = array_sum(array_map(function ($tax) {
                return $tax->amount;
            }, $batch)) / 100.0;

            $toPriceExcludedFactor = ($totalPercentage != -1)
                ? 1 / (1 + $totalPercentage)
                : 0.0;

            return $rawBase * $toPriceExcludedFactor * $this->amount / 100.0;
        }

        if ($this->amount_type === 'division') {
            return $rawBase * $this->amount / 100.0;
        }
    }

    public function evalTaxAmountPriceExcluded($batch, $rawBase, $evaluationContext)
    {
        if ($this->amount_type === 'percent') {
            return $rawBase * $this->amount / 100.0;
        }

        if ($this->amount_type === 'division') {
            $totalPercentage = array_sum(array_map(function ($tax) {
                return $tax->amount;
            }, $batch)) / 100.0;

            $inclBaseMultiplicator = ($totalPercentage == 1.0)
                ? 1.0
                : 1 - $totalPercentage;

            return $rawBase * $this->amount / 100.0 / $inclBaseMultiplicator;
        }
    }

    public function parentTaxes()
    {
        return $this->belongsToMany(self::class, 'accounts_tax_taxes', 'child_tax_id', 'parent_tax_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (self $tax) {
            $tax->attachDistributionForInvoice($tax);

            $tax->attachDistributionForRefund($tax);
        });
    }

    private function attachDistributionForInvoice(self $tax)
    {
        $distributionForInvoices = [
            [
                'tax_id'             => $tax->id,
                'company_id'         => $tax->company_id,
                'sort'               => 1,
                'creator_id'         => $tax->creator_id,
                'repartition_type'   => RepartitionType::BASE->value,
                'document_type'      => DocumentType::INVOICE->value,
                'use_in_tax_closing' => false,
                'factor_percent'     => null,
                'factor'             => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'tax_id'             => $tax->id,
                'company_id'         => $tax->company_id,
                'sort'               => 1,
                'creator_id'         => $tax->creator_id,
                'repartition_type'   => RepartitionType::TAX->value,
                'document_type'      => DocumentType::INVOICE->value,
                'use_in_tax_closing' => false,
                'factor_percent'     => 100,
                'factor'             => 1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ];

        DB::table('accounts_tax_partition_lines')->insert($distributionForInvoices);
    }

    private function attachDistributionForRefund(self $tax)
    {
        $distributionForRefunds = [
            [
                'tax_id'             => $tax->id,
                'company_id'         => $tax->company_id,
                'sort'               => 1,
                'creator_id'         => $tax->creator_id,
                'repartition_type'   => RepartitionType::BASE->value,
                'document_type'      => DocumentType::REFUND->value,
                'use_in_tax_closing' => false,
                'factor_percent'     => null,
                'factor'             => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'tax_id'             => $tax->id,
                'company_id'         => $tax->company_id,
                'sort'               => 1,
                'creator_id'         => $tax->creator_id,
                'repartition_type'   => RepartitionType::TAX->value,
                'document_type'      => DocumentType::REFUND->value,
                'use_in_tax_closing' => false,
                'factor_percent'     => 100,
                'factor'             => 1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ];

        DB::table('accounts_tax_partition_lines')->insert($distributionForRefunds);
    }
}
