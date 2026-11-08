<?php

namespace Webkul\Asset\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Database\Factories\AssetSiteTypeFactory;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class AssetSiteType extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'asset_site_types';

    protected $fillable = [
        'company_id',
        'name',
        'icon',
        'color',
    ];

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    protected static function booted(): void
    {
        static::creating(function (self $siteType): void {
            if (blank($siteType->icon)) {
                $siteType->icon = 'heroicon-m-map-pin';
            }

            if (blank($siteType->color)) {
                $siteType->color = '#3b82f6';
            }
        });

        static::deleting(function (self $siteType): void {
            if (AssetSite::withoutGlobalScopes()->withTrashed()->where('site_type_id', $siteType->id)->exists()) {
                throw ValidationException::withMessages([
                    'site_type_id' => __('assets::models/asset-site.deletion.referenced'),
                ]);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(AssetSite::class, 'site_type_id');
    }

    protected static function newFactory(): AssetSiteTypeFactory
    {
        return AssetSiteTypeFactory::new();
    }
}
