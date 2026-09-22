<?php

namespace Webkul\Product\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Database\Factories\PriceRuleItemFactory;
use Webkul\Product\Enums\PriceRuleApplyTo;
use Webkul\Product\Enums\PriceRuleBase;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Traits\BelongsToCompany;

class PriceRuleItem extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $table = 'products_price_rule_items';

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
        'price_max_margin',
        'percent_price',
        'starts_at',
        'ends_at',
        'price_list_id',
        'base_price_list_id',
        'currency_id',
        'product_id',
        'category_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'starts_at'        => 'datetime',
        'ends_at'          => 'datetime',
        'min_quantity'     => 'float',
        'fixed_price'      => 'float',
        'price_discount'   => 'float',
        'price_round'      => 'float',
        'price_surcharge'  => 'float',
        'price_markup'     => 'float',
        'price_min_margin' => 'float',
        'price_max_margin' => 'float',
        'percent_price'    => 'float',
        'apply_to'         => PriceRuleApplyTo::class,
        'base'             => PriceRuleBase::class,
        'type'             => PriceRuleType::class,
    ];

    /**
     * The stored scope follows what the rule actually targets: a category, a
     * product, one of its variants, or nothing at all, which covers everything.
     */
    public function computeApplyTo(): void
    {
        $displayApplyTo = $this->display_apply_to instanceof PriceRuleApplyTo
            ? $this->display_apply_to->value
            : $this->display_apply_to;

        $this->display_apply_to = $displayApplyTo === PriceRuleApplyTo::CATEGORY->value
            ? PriceRuleApplyTo::CATEGORY->value
            : PriceRuleApplyTo::PRODUCT->value;

        if ($this->display_apply_to === PriceRuleApplyTo::CATEGORY->value) {
            $this->product_id = null;

            $this->apply_to = $this->category_id
                ? PriceRuleApplyTo::CATEGORY
                : PriceRuleApplyTo::GLOBAL;

            return;
        }

        $this->category_id = null;

        if (! $this->product_id) {
            $this->apply_to = PriceRuleApplyTo::GLOBAL;

            return;
        }

        $isVariant = Product::query()
            ->withoutGlobalScopes()
            ->withTrashed()
            ->whereKey($this->product_id)
            ->value('parent_id');

        $this->apply_to = $isVariant
            ? PriceRuleApplyTo::VARIANT
            : PriceRuleApplyTo::PRODUCT;
    }

    /**
     * A discount rule reads its starting price from another price list as soon
     * as one is named, and a fixed price never reads from one at all.
     */
    public function computeBase(): void
    {
        if ($this->type === PriceRuleType::FIXED) {
            $this->base_price_list_id = null;
            $this->base = PriceRuleBase::LIST_PRICE;

            return;
        }

        if ($this->type === PriceRuleType::PERCENTAGE) {
            $this->base = $this->base_price_list_id
                ? PriceRuleBase::PRICE_RULES
                : PriceRuleBase::LIST_PRICE;

            return;
        }

        if ($this->base !== PriceRuleBase::PRICE_RULES) {
            $this->base_price_list_id = null;
        }
    }

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class, 'price_list_id');
    }

    public function basePriceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class, 'base_price_list_id');
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

    public function isApplicableFor(Product $product, float $quantity, array $categoryIds): bool
    {
        if ($this->min_quantity && $quantity < $this->min_quantity) {
            return false;
        }

        return match ($this->apply_to) {
            PriceRuleApplyTo::GLOBAL   => true,
            PriceRuleApplyTo::CATEGORY => in_array($this->category_id, $categoryIds, true),
            PriceRuleApplyTo::PRODUCT  => $this->matchesTemplate($product),
            PriceRuleApplyTo::VARIANT  => $this->matchesVariant($product),
        };
    }

    private function matchesTemplate(Product $product): bool
    {
        return $this->product_id === $product->id
            || $this->product_id === $product->parent_id;
    }

    private function matchesVariant(Product $product): bool
    {
        if ($this->product_id === $product->id) {
            return true;
        }

        if (! $product->is_configurable) {
            return false;
        }

        $variantIds = $product->variants()->pluck('id');

        return $variantIds->count() === 1 && $variantIds->first() === $this->product_id;
    }

    public function isEffectiveOn(DateTimeInterface $date): bool
    {
        if ($this->starts_at && $this->starts_at->greaterThan($date)) {
            return false;
        }

        return ! ($this->ends_at && $this->ends_at->lessThan($date));
    }

    protected static function newFactory(): PriceRuleItemFactory
    {
        return PriceRuleItemFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($priceRuleItem) {
            $priceRuleItem->creator_id ??= Auth::id();
        });

        static::saving(function (PriceRuleItem $priceRuleItem) {
            if (blank($priceRuleItem->type)) {
                $priceRuleItem->type = PriceRuleType::FIXED;
            }

            if (blank($priceRuleItem->base)) {
                $priceRuleItem->base = PriceRuleBase::LIST_PRICE;
            }

            if ($priceRuleItem->price_list_id) {
                $priceList = PriceList::query()
                    ->withoutGlobalScopes()
                    ->whereKey($priceRuleItem->price_list_id)
                    ->first(['company_id', 'currency_id']);

                $priceRuleItem->company_id = $priceList?->company_id;
                $priceRuleItem->currency_id = $priceList?->currency_id;
            }

            if (blank($priceRuleItem->display_apply_to)) {
                $priceRuleItem->display_apply_to = $priceRuleItem->apply_to === PriceRuleApplyTo::CATEGORY
                    ? PriceRuleApplyTo::CATEGORY->value
                    : PriceRuleApplyTo::PRODUCT->value;
            }

            $priceRuleItem->computeApplyTo();

            $priceRuleItem->computeBase();
        });
    }
}
