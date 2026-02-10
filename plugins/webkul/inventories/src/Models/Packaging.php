<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webkul\Product\Models\Packaging as BasePackaging;

class Packaging extends BasePackaging
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->mergeFillable([]);

        $this->mergeCasts([]);
    }

    public function packageType(): BelongsTo
    {
        return $this->belongsTo(PackageType::class);
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'inventories_route_packagings', 'packaging_id', 'route_id');
    }
}
