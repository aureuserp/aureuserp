<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Product\Database\Factories\ProductFactory;
use Webkul\Product\Enums\ProductType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class Product extends Model implements Sortable
{
    use HasChatter, HasFactory, HasLogActivity, SoftDeletes, SortableTrait;

    protected $table = 'products_products';

    protected $fillable = [
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
        'images',
        'sort',
        'parent_id',
        'uom_id',
        'uom_po_id',
        'category_id',
        'company_id',
        'creator_id',
    ];

    public function getModelTitle(): string
    {
        return __('products::models/product.title');
    }

    protected $casts = [
        'type'             => ProductType::class,
        'enable_sales'     => 'boolean',
        'enable_purchase'  => 'boolean',
        'is_favorite'      => 'boolean',
        'is_configurable'  => 'boolean',
        'images'           => 'array',
        'cost'             => 'float',
        'price'            => 'float',
        'volume'           => 'decimal:4',
        'weight'           => 'decimal:4',
    ];

    protected function getLogAttributeLabels(): array
    {
        return [
            'type'                 => __('products::models/product.log-attributes.type'),
            'name'                 => __('products::models/product.log-attributes.name'),
            'service_tracking'     => __('products::models/product.log-attributes.service_tracking'),
            'reference'            => __('products::models/product.log-attributes.reference'),
            'barcode'              => __('products::models/product.log-attributes.barcode'),
            'price'                => __('products::models/product.log-attributes.price'),
            'cost'                 => __('products::models/product.log-attributes.cost'),
            'volume'               => __('products::models/product.log-attributes.volume'),
            'weight'               => __('products::models/product.log-attributes.weight'),
            'description'          => __('products::models/product.log-attributes.description'),
            'description_purchase' => __('products::models/product.log-attributes.description_purchase'),
            'description_sale'     => __('products::models/product.log-attributes.description_sale'),
            'enable_sales'         => __('products::models/product.log-attributes.enable_sales'),
            'enable_purchase'      => __('products::models/product.log-attributes.enable_purchase'),
            'is_favorite'          => __('products::models/product.log-attributes.is_favorite'),
            'is_configurable'      => __('products::models/product.log-attributes.is_configurable'),
            'parent.name'          => __('products::models/product.log-attributes.parent'),
            'category.name'        => __('products::models/product.log-attributes.category'),
            'company.name'         => __('products::models/product.log-attributes.company'),
            'creator.name'         => __('products::models/product.log-attributes.creator'),
        ];
    }

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }

    public function uomPO(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'products_product_tag', 'product_id', 'tag_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function attribute_values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function combinations(): HasMany
    {
        return $this->hasMany(ProductCombination::class, 'product_id');
    }

    public function priceRuleItems(): HasMany
    {
        return $this->hasMany(PriceRuleItem::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    public function supplierInformation(): HasMany
    {
        if ($this->is_configurable) {
            return $this->hasMany(ProductSupplier::class)
                ->orWhereIn('product_id', $this->variants()->pluck('id'));
        } else {
            return $this->hasMany(ProductSupplier::class);
        }
    }

    /**
     * Generate or sync variants based on product attributes
     */
    public function generateVariants(): void
    {
        $attributes = $this->attributes()->with(['values', 'attribute', 'options'])->get();

        if ($attributes->isEmpty()) {
            return;
        }

        $existingVariants = $this->variants()->get();
        
        $generateCombinations = function ($attrs, $current = [], $index = 0) use (&$generateCombinations) {
            if ($index >= $attrs->count()) {
                return [$current];
            }

            return collect($attrs[$index]->values)
                ->flatMap(fn($value) => $generateCombinations($attrs, array_merge($current, [$value]), $index + 1))
                ->all();
        };

        $getVariantDetails = function ($combination) {
            $name = $this->name . ' - ' . collect($combination)
                ->map(fn($value) => $value->attributeOption->name)
                ->implode(' / ');

            $price = $this->price + collect($combination)->sum('extra_price');

            return compact('name', 'price');
        };

        $findVariant = function ($combination) use ($existingVariants) {
            $combinationIds = collect($combination)->pluck('id')->sort()->values()->toArray();

            return $existingVariants->first(function ($variant) use ($combinationIds) {
                $variantIds = ProductCombination::where('product_id', $variant->id)
                    ->pluck('product_attribute_value_id')
                    ->sort()
                    ->values()
                    ->toArray();

                return $combinationIds === $variantIds;
            });
        };

        $syncCombinations = function ($variant, $combination) {
            ProductCombination::where('product_id', $variant->id)->delete();

            collect($combination)->each(fn($value) => ProductCombination::create([
                'product_id'                 => $variant->id,
                'product_attribute_value_id' => $value->id,
            ]));
        };

        $processedVariantIds = collect($generateCombinations($attributes))
            ->map(function ($combination) use ($findVariant, $getVariantDetails, $syncCombinations) {
                $variant = $findVariant($combination);
                $details = $getVariantDetails($combination);

                if ($variant) {
                    $variant->update($details);
                } else {
                    $variant = self::create([
                        'type'                 => $this->type,
                        'name'                 => $details['name'],
                        'price'                => $details['price'],
                        'cost'                 => $this->cost,
                        'enable_sales'         => $this->enable_sales,
                        'enable_purchase'      => $this->enable_purchase,
                        'parent_id'            => $this->id,
                        'company_id'           => $this->company_id,
                        'creator_id'           => Auth::id(),
                        'uom_id'               => $this->uom_id,
                        'uom_po_id'            => $this->uom_po_id,
                        'category_id'          => $this->category_id,
                        'volume'               => $this->volume,
                        'weight'               => $this->weight,
                        'description'          => $this->description,
                        'description_purchase' => $this->description_purchase,
                        'description_sale'     => $this->description_sale,
                        'barcode'              => null,
                        'reference'            => $this->reference . '-' . strtolower(str_replace(' ', '-', $details['name'])),
                        'images'               => $this->images,
                    ]);
                }

                $syncCombinations($variant, $combination);

                return $variant->id;
            })
            ->all();

        $existingVariants
            ->whereNotIn('id', $processedVariantIds)
            ->each(function ($variant) {
                ProductCombination::where('product_id', $variant->id)->delete();
                $variant->forceDelete();
            });

        $this->update(['is_configurable' => true]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->creator_id = Auth::id();
        });

        static::saved(function ($product) {
            $product->variants->each(fn ($variant) => $variant->update(['is_storable' => $product->is_storable]));
        });
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
