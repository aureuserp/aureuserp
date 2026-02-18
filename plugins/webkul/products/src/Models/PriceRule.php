<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Product\Database\Factories\PriceRuleFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class PriceRule extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'products_price_rules';

    protected $fillable = [
        'name',
        'sort',
        'currency_id',
        'company_id',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceRuleItem::class);
    }

    protected static function newFactory(): PriceRuleFactory
    {
        return PriceRuleFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($priceRule) {
            $priceRule->creator_id ??= Auth::id();
        });
    }
}
