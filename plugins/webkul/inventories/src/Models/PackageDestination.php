<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\PackageDestinationFactory;
use Webkul\Security\Models\User;

class PackageDestination extends Model
{
    use HasFactory;

    protected $table = 'inventories_package_destinations';

    protected $fillable = [
        'operation_id',
        'destination_location_id',
        'creator_id',
    ];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): PackageDestinationFactory
    {
        return PackageDestinationFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($packageDestination) {
            $packageDestination->creator_id ??= Auth::id();
        });
    }
}
