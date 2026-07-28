<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Inventory\Models\ProcurementGroup as BaseProcurementGroup;

class ProcurementGroup extends BaseProcurementGroup
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'procurement_group_id');
    }
}
