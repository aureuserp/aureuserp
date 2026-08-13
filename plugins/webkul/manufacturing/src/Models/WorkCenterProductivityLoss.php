<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Security\Models\User;

class WorkCenterProductivityLoss extends Model
{
    protected $table = 'manufacturing_work_center_productivity_losses';

    protected $fillable = [
        'sort',
        'loss_type',
        'name',
        'manual',
        'loss_type_id',
        'creator_id',
    ];

    protected $casts = [
        'manual' => 'boolean',
    ];

    public function getModelTitle(): string
    {
        return __('manufacturing::models/work-center-productivity-loss.title');
    }

    public function lossType(): BelongsTo
    {
        return $this->belongsTo(WorkCenterLossType::class, 'loss_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function productivityLogs(): HasMany
    {
        return $this->hasMany(WorkCenterProductivityLog::class, 'loss_id');
    }

    public function convertToDuration($startDate, $endDate, $workCenter = null): float
    {
        $duration = 0;

        if (
            ! in_array($this->loss_type, ['productive', 'performance'])
            && $workCenter?->calendar_id
        ) {
            $data = $workCenter->getWorkDaysDataBatch($startDate, $endDate);

            $hours = $data[$workCenter->id]['hours'] ?? 0;

            $duration = max($duration, $hours * 60);
        } else {
            $minutes = ($endDate->getTimestamp() - $startDate->getTimestamp()) / 60;

            $duration = max($duration, $minutes);
        }

        return round($duration, 2);
    }
}
