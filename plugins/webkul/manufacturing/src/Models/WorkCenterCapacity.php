<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class WorkCenterCapacity extends Model
{
    protected $table = 'manufacturing_work_center_capacities';

    protected $fillable = [
        'work_center_id',
        'product_id',
        'capacity',
        'time_start',
        'time_stop',
        'creator_id',
    ];

    protected $casts = [
        'capacity'   => 'decimal:4',
        'time_start' => 'decimal:4',
        'time_stop'  => 'decimal:4',
    ];

    public function workCenter(): BelongsTo
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $capacity): void {
            $capacity->creator_id ??= Auth::id();
            $capacity->capacity ??= 1;
            $capacity->time_start ??= 0;
            $capacity->time_stop ??= 0;
        });
    }
}
