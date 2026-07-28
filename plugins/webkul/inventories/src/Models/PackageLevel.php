<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\PackageLevelFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class PackageLevel extends Model
{
    use HasFactory;

    protected $table = 'inventories_package_levels';

    protected $fillable = [
        'package_id',
        'operation_id',
        'destination_location_id',
        'company_id',
        'creator_id',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function moveLines(): HasMany
    {
        return $this->hasMany(MoveLine::class, 'package_level_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): PackageLevelFactory
    {
        return PackageLevelFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($packageLevel) {
            $packageLevel->creator_id ??= Auth::id();
        });
    }
}
