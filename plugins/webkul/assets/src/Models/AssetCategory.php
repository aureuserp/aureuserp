<?php

namespace Webkul\Asset\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Database\Factories\AssetCategoryFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Support\CompanyConsistencyGuard;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class AssetCategory extends Model
{
    use BelongsToCompany, ChecksCompanyConsistency, HasFactory;

    protected $table = 'asset_categories';

    protected $fillable = [
        'company_id',
        'parent_id',
        'name',
        'full_name',
        'parent_path',
        'creator_id',
    ];

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    public function companyConsistentFields(): array
    {
        return [
            'parent_id' => self::class,
        ];
    }

    /**
     * Apply a hierarchical filter matching the given categories and all their descendants.
     *
     * @param  int|array<int>  $categoryIds
     */
    public static function applySubtreeFilter(Builder $query, int|array $categoryIds, string $column = 'category_id'): Builder
    {
        $categoryIds = array_values(array_unique(array_map(
            fn ($id): int => (int) $id,
            Arr::wrap($categoryIds),
        )));

        if ($categoryIds === []) {
            return $query;
        }

        $categories = static::withoutGlobalScopes()
            ->whereIn('id', $categoryIds)
            ->get(['id', 'parent_path']);

        $ids = static::withoutGlobalScopes()
            ->where(function (Builder $q) use ($categories, $categoryIds): void {
                $q->whereIn('id', $categoryIds);

                foreach ($categories as $category) {
                    $q->orWhere('parent_path', 'like', ($category->parent_path ?: '/').$category->id.'/%');
                }
            })
            ->pluck('id')
            ->all();

        return $query->whereIn($column, array_values(array_unique(array_merge($categoryIds, $ids))));
    }

    protected static function booted(): void
    {
        static::creating(function (self $category): void {
            if (blank($category->creator_id)) {
                $category->creator_id = Auth::id();
            }

            if (filled($category->company_id)) {
                CompanyConsistencyGuard::assert(
                    $category->company_id,
                    [$category->getAttributes()],
                    $category->companyConsistentFields(),
                );
            }

            if (! static::validateNoRecursion($category)) {
                throw ValidationException::withMessages([
                    'parent_id' => __('assets::models/asset.category.circular-reference'),
                ]);
            }

            static::syncHierarchyAttributes($category);
        });

        static::updating(function (self $category): void {
            if (filled($category->company_id)) {
                CompanyConsistencyGuard::assert(
                    $category->company_id,
                    [$category->getAttributes()],
                    $category->companyConsistentFields(),
                );
            }

            if (! static::validateNoRecursion($category)) {
                throw ValidationException::withMessages([
                    'parent_id' => __('assets::models/asset.category.circular-reference'),
                ]);
            }

            static::syncHierarchyAttributes($category);
        });

        static::updated(function (self $category): void {
            if ($category->wasChanged(['name', 'parent_id', 'full_name', 'parent_path'])) {
                $category->children()->withoutGlobalScopes()->get()->each->save();
            }
        });

        static::deleting(function (self $category): void {
            $hasChildren = static::withoutGlobalScopes()->where('parent_id', $category->id)->exists();
            $hasProducts = AssetProduct::withoutGlobalScopes()->withTrashed()->where('category_id', $category->id)->exists();

            if ($hasChildren || $hasProducts) {
                throw ValidationException::withMessages([
                    'category_id' => __('assets::models/asset.deletion.category-referenced'),
                ]);
            }
        });
    }

    protected static function validateNoRecursion(self $category): bool
    {
        if (! $category->parent_id) {
            return true;
        }

        if ($category->exists && (int) $category->id === (int) $category->parent_id) {
            return false;
        }

        $visitedIds = [$category->exists ? (int) $category->id : -1];
        $currentParentId = (int) $category->parent_id;

        while ($currentParentId) {
            if (in_array($currentParentId, $visitedIds, true)) {
                return false;
            }

            $visitedIds[] = $currentParentId;
            $parent = static::withoutGlobalScopes()->find($currentParentId);

            if (! $parent) {
                break;
            }

            $currentParentId = (int) $parent->parent_id;
        }

        return true;
    }

    protected static function syncHierarchyAttributes(self $category): void
    {
        if ($category->parent_id) {
            $parent = static::withoutGlobalScopes()->find($category->parent_id);

            if ($parent) {
                $category->parent_path = ($parent->parent_path ?: '/').$parent->id.'/';
                $category->full_name = ($parent->full_name ?: $parent->name).' / '.$category->name;

                return;
            }

            $category->parent_id = null;
        }

        $category->parent_path = '/';
        $category->full_name = $category->name;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(AssetProduct::class, 'category_id');
    }

    public function assets(): HasManyThrough
    {
        return $this->hasManyThrough(Asset::class, AssetProduct::class, 'category_id', 'product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory(): AssetCategoryFactory
    {
        return AssetCategoryFactory::new();
    }
}
