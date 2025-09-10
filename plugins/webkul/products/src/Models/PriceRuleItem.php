<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Database\Factories\PriceRuleItemFactory;
use Webkul\Product\Enums\PriceRuleApplyTo;
use Webkul\Product\Enums\PriceRuleBase;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class PriceRuleItem extends Model
{
    use HasFactory;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'products_price_rule_items';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'apply_to',
        'display_apply_to',
        'base',
        'type',
        'min_quantity',
        'fixed_price',
        'price_discount',
        'price_round',
        'price_surcharge',
        'price_markup',
        'price_min_margin',
        'percent_price',
        'starts_at',
        'ends_at',
        'price_rule_id',
        'base_price_rule_id',
        'currency_id',
        'product_id',
        'category_id',
        'company_id',
        'creator_id',
    ];

    /**
     * Casts
     *
     * @var string
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'apply_to'  => PriceRuleApplyTo::class,
        'base'      => PriceRuleBase::class,
        'type'      => PriceRuleType::class,
    ];

    public function priceRule(): BelongsTo
    {
        return $this->belongsTo(PriceRule::class);
    }

    public function basePriceRule(): BelongsTo
    {
        return $this->belongsTo(PriceRule::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

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

    protected static function newFactory(): PriceRuleItemFactory
    {
        return PriceRuleItemFactory::new();
    }
}
