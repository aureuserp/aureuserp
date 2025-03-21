<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Support\Models\ActivityPlan as BaseActivityPlan;

class ActivityPlan extends BaseActivityPlan
{
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
