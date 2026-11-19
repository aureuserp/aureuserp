<?php

namespace Webkul\Asset\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Database\Factories\AssetSiteFactory;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Employee;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;
use Webkul\Support\Support\CompanyConsistencyGuard;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class AssetSite extends Model
{
    use BelongsToCompany;
    use ChecksCompanyConsistency, HasChatter, HasCustomFields, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'asset_sites';

    protected $fillable = [
        'company_id',
        'parent_id',
        'site_type_id',
        'usage_type',
        'name',
        'full_name',
        'parent_path',
        'code',
        'default_custodian_employee_id',
        'is_active',
        'note',
        'creator_id',
    ];

    protected $casts = [
        'usage_type' => AssetSiteUsage::class,
        'is_active'  => 'boolean',
    ];

    /**
     * Resolve the given site(s) and all of their descendants.
     *
     * @param  int|array<int>  $siteIds
     * @return array<int, int>
     */
    public static function subtreeIds(int|array $siteIds): array
    {
        $siteIds = array_values(array_unique(array_map(
            fn ($id): int => (int) $id,
            Arr::wrap($siteIds),
        )));

        if ($siteIds === []) {
            return [];
        }

        $sites = static::withoutGlobalScopes()
            ->withTrashed()
            ->whereIn('id', $siteIds)
            ->get(['id', 'parent_path']);

        $ids = static::withoutGlobalScopes()
            ->withTrashed()
            ->where(function (Builder $q) use ($sites, $siteIds): void {
                $q->whereIn('id', $siteIds);

                foreach ($sites as $site) {
                    $q->orWhere('parent_path', 'like', ($site->parent_path ?: '/').$site->id.'/%');
                }
            })
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        return array_values(array_unique(array_merge($siteIds, $ids)));
    }

    /**
     * @param  int|array<int>  $siteIds
     */
    public static function applySubtreeFilter(Builder $query, int|array $siteIds, string $column = 'current_site_id'): Builder
    {
        $subtreeIds = static::subtreeIds($siteIds);

        if ($subtreeIds === []) {
            return $query;
        }

        return $query->whereIn($column, $subtreeIds);
    }

    protected static function booted(): void
    {
        static::creating(function (self $site): void {
            if (blank($site->company_id)) {
                $site->company_id = app(CompanyContext::class)->currentId();
            }

            if (blank($site->creator_id)) {
                $site->creator_id = Auth::id();
            }

            if (blank($site->usage_type)) {
                $site->usage_type = AssetSiteUsage::AssetLocation;
            }

            if (! static::validateNoRecursion($site)) {
                throw ValidationException::withMessages([
                    'parent_id' => __('assets::models/asset-site.hierarchy.circular-reference'),
                ]);
            }

            static::syncHierarchyAndUsage($site);

            CompanyConsistencyGuard::assert(
                $site->company_id,
                [$site->getAttributes()],
                $site->companyConsistentFields(),
            );
        });

        static::updating(function (self $site): void {
            if (! static::validateNoRecursion($site)) {
                throw ValidationException::withMessages([
                    'parent_id' => __('assets::models/asset-site.hierarchy.circular-reference'),
                ]);
            }

            static::syncHierarchyAndUsage($site);

            CompanyConsistencyGuard::assert(
                $site->company_id,
                [$site->getAttributes()],
                $site->companyConsistentFields(),
            );

            $subtreeIds = static::subtreeIds($site->id);

            if ($site->isDirty('company_id')) {
                $hasChildren = static::withoutGlobalScopes()->withTrashed()->where('parent_id', $site->id)->exists();
                $isReferenced = $hasChildren
                    || Asset::withoutGlobalScopes()->withTrashed()->whereIn('current_site_id', $subtreeIds)->exists()
                    || AssetMovement::withoutGlobalScopes()->where(function (Builder $q) use ($subtreeIds): void {
                        $q->whereIn('from_site_id', $subtreeIds)
                            ->orWhereIn('to_site_id', $subtreeIds);
                    })->exists();

                if ($isReferenced) {
                    throw ValidationException::withMessages([
                        'company_id' => __('assets::models/asset-site.company.immutable'),
                    ]);
                }
            }

            if ($site->isDirty('is_active') && ! $site->is_active) {
                if (Asset::withoutGlobalScopes()->where('status', AssetStatus::Held)->whereIn('current_site_id', $subtreeIds)->exists()) {
                    throw ValidationException::withMessages([
                        'is_active' => __('assets::models/asset-site.deactivation.open-custody'),
                    ]);
                }
            }

            if ($site->isDirty('usage_type') && $site->usage_type === AssetSiteUsage::CustodyZone) {
                if (Asset::withoutGlobalScopes()->where('status', AssetStatus::Held)->whereNull('current_employee_id')->whereIn('current_site_id', $subtreeIds)->exists()) {
                    throw ValidationException::withMessages([
                        'usage_type' => __('assets::models/asset-site.usage.has-fixed-assets'),
                    ]);
                }
            }

            if ($site->isDirty('usage_type') && $site->usage_type === AssetSiteUsage::AssetLocation) {
                if (Asset::withoutGlobalScopes()->where('status', AssetStatus::Held)->whereNotNull('current_employee_id')->whereIn('current_site_id', $subtreeIds)->exists()) {
                    throw ValidationException::withMessages([
                        'usage_type' => __('assets::models/asset-site.usage.has-custody-assets'),
                    ]);
                }
            }
        });

        static::updated(function (self $site): void {
            if ($site->wasChanged('is_active') && ! $site->is_active) {
                $descendantIds = array_values(array_diff(static::subtreeIds($site->id), [$site->id]));

                if (! empty($descendantIds)) {
                    static::withoutGlobalScopes()->whereIn('id', $descendantIds)->update(['is_active' => false]);
                }
            }

            if ($site->wasChanged('usage_type')) {
                $descendantIds = array_values(array_diff(static::subtreeIds($site->id), [$site->id]));

                if (! empty($descendantIds)) {
                    static::withoutGlobalScopes()->whereIn('id', $descendantIds)->update(['usage_type' => $site->usage_type]);
                }
            }

            if ($site->wasChanged(['name', 'parent_id', 'full_name', 'parent_path'])) {
                $site->children()->withoutGlobalScopes()->get()->each(function (self $child): void {
                    static::syncHierarchyAndUsage($child);
                    $child->save();
                });
            }
        });

        static::deleting(function (self $site): void {
            if ($site->isForceDeleting()) {
                $hasChildren = static::withoutGlobalScopes()->withTrashed()->where('parent_id', $site->id)->exists();
                $hasAssets = Asset::withoutGlobalScopes()
                    ->withTrashed()
                    ->where('current_site_id', $site->id)
                    ->exists();

                $hasMovements = AssetMovement::withoutGlobalScopes()
                    ->where(function (Builder $q) use ($site): void {
                        $q->where('from_site_id', $site->id)
                            ->orWhere('to_site_id', $site->id);
                    })
                    ->exists();

                if ($hasChildren || $hasAssets || $hasMovements) {
                    throw ValidationException::withMessages([
                        'site' => __('assets::models/asset-site.deletion.referenced'),
                    ]);
                }

                return;
            }

            $hasActiveChildren = static::withoutGlobalScopes()->whereNull('deleted_at')->where('parent_id', $site->id)->exists();
            $hasActiveAssets = Asset::withoutGlobalScopes()->where('current_site_id', $site->id)->exists();

            if ($hasActiveChildren || $hasActiveAssets) {
                throw ValidationException::withMessages([
                    'site' => __('assets::models/asset-site.deletion.referenced'),
                ]);
            }
        });

        static::restoring(function (self $site): void {
            if ($site->parent_id) {
                $parent = static::withoutGlobalScopes()->withTrashed()->find($site->parent_id);

                if ($parent && $parent->trashed()) {
                    $parent->restore();
                }
            }
        });
    }

    protected static function validateNoRecursion(self $site): bool
    {
        if (! $site->parent_id) {
            return true;
        }

        if ($site->exists && (int) $site->id === (int) $site->parent_id) {
            return false;
        }

        $visitedIds = [$site->exists ? (int) $site->id : -1];
        $currentParentId = (int) $site->parent_id;

        while ($currentParentId) {
            if (in_array($currentParentId, $visitedIds, true)) {
                return false;
            }

            $visitedIds[] = $currentParentId;
            $parent = static::withoutGlobalScopes()->withTrashed()->find($currentParentId);

            if (! $parent) {
                break;
            }

            $currentParentId = (int) $parent->parent_id;
        }

        return true;
    }

    protected static function syncHierarchyAndUsage(self $site): void
    {
        if ($site->parent_id) {
            $parent = static::withoutGlobalScopes()->withTrashed()->find($site->parent_id);

            if ($parent) {
                $site->usage_type = $parent->usage_type;
                $site->parent_path = ($parent->parent_path ?: '/').$parent->id.'/';
                $site->full_name = ($parent->full_name ?: $parent->name).' / '.$site->name;

                return;
            }

            $site->parent_id = null;
        }

        $site->parent_path = '/';
        $site->full_name = $site->name;
    }

    public function getModelTitle(): string
    {
        return __('assets::models/asset-site.title');
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

    public function siteType(): BelongsTo
    {
        return $this->belongsTo(AssetSiteType::class, 'site_type_id');
    }

    public function defaultCustodian(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'default_custodian_employee_id')->withTrashed();
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'current_site_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function companyConsistentFields(): array
    {
        return [
            'parent_id'                     => self::class,
            'site_type_id'                  => AssetSiteType::class,
            'default_custodian_employee_id' => Employee::class,
        ];
    }

    protected static function newFactory(): AssetSiteFactory
    {
        return AssetSiteFactory::new();
    }
}
