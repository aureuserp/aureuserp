<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Security\Models\User;

class WorkCenterLossType extends Model
{
    protected $table = 'manufacturing_work_center_loss_types';

    protected $fillable = [
        'loss_type',
        'creator_id',
    ];

    public function getModelTitle(): string
    {
        return __('manufacturing::models/work-center-loss-type.title');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function productivityLosses(): HasMany
    {
        return $this->hasMany(WorkCenterProductivityLoss::class, 'loss_type_id');
    }
}
