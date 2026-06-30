<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Inventory\Models\Lot as BaseLot;

class Lot extends BaseLot
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function moveLines(): HasMany
    {
        return $this->hasMany(MoveLine::class);
    }
}
