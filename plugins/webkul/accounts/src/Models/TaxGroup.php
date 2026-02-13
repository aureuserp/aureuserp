<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Webkul\Account\Database\Factories\TaxGroupFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;

class TaxGroup extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'accounts_tax_groups';

    protected $fillable = [
        'sort',
        'company_id',
        'country_id',
        'creator_id',
        'name',
        'preceding_subtotal',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($taxGroup) {
            $taxGroup->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): TaxGroupFactory
    {
        return TaxGroupFactory::new();
    }
}
