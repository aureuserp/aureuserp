<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Reconcile extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'accounts_reconciles';

    protected $fillable = [
        'sort',
        'company_id',
        'past_months_limit',
        'created_by',
        'rule_type',
        'matching_order',
        'counter_part_type',
        'match_nature',
        'match_amount',
        'match_label',
        'match_level_parameters',
        'match_note',
        'match_note_parameters',
        'match_transaction_type',
        'match_transaction_type_parameters',
        'payment_tolerance_type',
        'decimal_separator',
        'name',
        'auto_reconcile',
        'to_check',
        'match_text_location_label',
        'match_text_location_note',
        'match_text_location_reference',
        'match_same_currency',
        'allow_payment_tolerance',
        'match_partner',
        'match_amount_min',
        'match_amount_max',
        'payment_tolerance_parameters',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
