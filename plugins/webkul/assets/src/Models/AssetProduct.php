<?php

namespace Webkul\Asset\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Database\Factories\AssetProductFactory;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;
use Webkul\Support\Support\CompanyConsistencyGuard;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class AssetProduct extends Model
{
    use BelongsToCompany;
    use ChecksCompanyConsistency, HasChatter, HasCustomFields, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'asset_products';

    protected $fillable = [
        'company_id',
        'category_id',
        'type',
        'name',
        'model',
        'cost',
        'is_active',
        'note',
        'creator_id',
    ];

    protected $casts = [
        'type'      => AssetProductType::class,
        'cost'      => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function isConsumable(): bool
    {
        return $this->type === AssetProductType::Consumable;
    }

    public function getModelTitle(): string
    {
        return __('assets::filament/resources/product.label');
    }

    public function companyConsistentFields(): array
    {
        return [
            'category_id' => AssetCategory::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $product): void {
            if (blank($product->company_id)) {
                $product->company_id = app(CompanyContext::class)->currentId();
            }

            if (blank($product->creator_id)) {
                $product->creator_id = Auth::id();
            }

            if (blank($product->type)) {
                $product->type = AssetProductType::Asset;
            }

            if (blank($product->company_id)) {
                throw ValidationException::withMessages([
                    'company_id' => __('assets::services/asset-custody.company-required'),
                ]);
            }

            CompanyConsistencyGuard::assert(
                $product->company_id,
                [$product->getAttributes()],
                $product->companyConsistentFields(),
            );
        });

        static::updating(function (self $product): void {
            if ($product->isDirty('type')) {
                $hasAssets = Asset::withoutGlobalScopes()
                    ->withTrashed()
                    ->where('product_id', $product->id)
                    ->exists();

                if ($hasAssets) {
                    throw ValidationException::withMessages([
                        'type' => __('assets::models/asset.product-type-immutable'),
                    ]);
                }
            }

            if ($product->isDirty('company_id')) {
                $isReferenced = Asset::withoutGlobalScopes()
                    ->withTrashed()
                    ->where('product_id', $product->id)
                    ->exists();

                if ($isReferenced) {
                    throw ValidationException::withMessages([
                        'company_id' => __('assets::models/asset.company.immutable'),
                    ]);
                }
            }

            if ($product->isDirty('is_active') && ! $product->is_active) {
                $hasOpenAssets = Asset::withoutGlobalScopes()
                    ->where('product_id', $product->id)
                    ->whereIn('status', [AssetStatus::Available, AssetStatus::Held])
                    ->exists();

                if ($hasOpenAssets) {
                    throw ValidationException::withMessages([
                        'is_active' => __('assets::models/asset.deletion.product-in-use', ['product' => $product->name]),
                    ]);
                }
            }

            if (filled($product->company_id)) {
                CompanyConsistencyGuard::assert(
                    $product->company_id,
                    [$product->getAttributes()],
                    $product->companyConsistentFields(),
                );
            }
        });

        static::deleting(function (self $product): void {
            if ($product->isForceDeleting()) {
                $isReferenced = Asset::withoutGlobalScopes()
                    ->withTrashed()
                    ->where('product_id', $product->id)
                    ->exists();

                if ($isReferenced) {
                    throw ValidationException::withMessages([
                        'product_id' => __('assets::models/asset.deletion.product-in-use', ['product' => $product->name]),
                    ]);
                }

                return;
            }

            $hasOpenAssets = Asset::withoutGlobalScopes()
                ->where('product_id', $product->id)
                ->whereIn('status', [AssetStatus::Available, AssetStatus::Held])
                ->exists();

            if ($hasOpenAssets) {
                throw ValidationException::withMessages([
                    'product_id' => __('assets::models/asset.deletion.product-in-use', ['product' => $product->name]),
                ]);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory(): AssetProductFactory
    {
        return AssetProductFactory::new();
    }
}
