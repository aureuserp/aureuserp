<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\PackageFactory;
use Webkul\Inventory\Enums\PackageUse;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Package extends Model
{
    use HasFactory;

    protected $table = 'inventories_packages';

    protected $fillable = [
        'name',
        'package_use',
        'pack_date',
        'package_type_id',
        'location_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'package_use' => PackageUse::class,
        'pack_date'   => 'date',
    ];

    public function packageType(): BelongsTo
    {
        return $this->belongsTo(PackageType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function quantities(): HasMany
    {
        return $this->hasMany(ProductQuantity::class);
    }

    public function operations(): HasManyThrough
    {
        return $this->hasManyThrough(
            Operation::class,
            MoveLine::class,
            'result_package_id',
            'id',
            'id',
            'operation_id'
        );
    }

    public function moves(): HasManyThrough
    {
        return $this->hasManyThrough(
            Move::class,
            MoveLine::class,
            'package_id',
            'id',
            'id',
            'move_id'
        );
    }

    public function moveLines(): HasMany
    {
        return $this->hasMany(MoveLine::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): PackageFactory
    {
        return PackageFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            $authUser = Auth::user();

            $package->creator_id ??= $authUser?->id;

            $package->company_id ??= $authUser?->default_company_id;
        });
    }

    public function checkMoveLinesMapQuant($moveLines): bool
    {
        $groupedQuantities = $this->quantities
            ->groupBy(fn ($quantity) => $quantity->product_id.'-'.$quantity->lot_id)
            ->map(fn ($quantities) => $quantities->sum('quantity'));

        $groupedOps = $moveLines
            ->groupBy(fn ($moveLine) => $moveLine->product_id.'-'.$moveLine->lot_id)
            ->map(fn ($moveLines) => $moveLines->sum('qty'));

        foreach ($groupedQuantities as $key => $quantity) {
            if (! float_is_zero($quantity - ($groupedOps[$key] ?? 0), 2)) {
                return false;
            }
        }

        foreach ($groupedOps as $key => $quantity) {
            if (! float_is_zero($quantity - ($groupedQuantities[$key] ?? 0), 2)) {
                return false;
            }
        }

        return true;
    }
}
