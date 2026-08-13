<?php

namespace Webkul\Manufacturing\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Manufacturing\Database\Factories\WorkCenterProductivityLogFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class WorkCenterProductivityLog extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $table = 'manufacturing_work_center_productivity_logs';

    protected $fillable = [
        'loss_type',
        'description',
        'started_at',
        'finished_at',
        'duration',
        'work_center_id',
        'company_id',
        'work_order_id',
        'assigned_user_id',
        'loss_id',
        'creator_id',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
        'duration'    => 'decimal:4',
    ];

    protected static function newFactory(): WorkCenterProductivityLogFactory
    {
        return WorkCenterProductivityLogFactory::new();
    }

    public function getModelTitle(): string
    {
        return __('manufacturing::models/work-center-productivity-log.title');
    }

    public function workCenter(): BelongsTo
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id')->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function loss(): BelongsTo
    {
        return $this->belongsTo(WorkCenterProductivityLoss::class, 'loss_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $productivityLog): void {
            $user = Auth::user();

            $productivityLog->creator_id ??= $user?->id;

            $productivityLog->assigned_user_id ??= $user?->id;

            $productivityLog->company_id ??= current_company_id();

            $productivityLog->description ??= __('manufacturing::system.work-center-productivity-log.time-tracking', ['name' => $user->name]);

            $productivityLog->loss_type ??= $productivityLog->loss->loss_type ?? 'other';

            $productivityLog->work_center_id ??= $productivityLog->workOrder->work_center_id ?? null;
        });

        static::created(function (self $productivityLog): void {
            if ($workCenter = $productivityLog->workCenter) {
                $workCenter->computeWorkingState();

                $workCenter->save();
            }

            if ($productivityLog->duration) {
                $productivityLog->workOrder->computeDuration();

                $productivityLog->workOrder->save();
            }
        });

        static::saving(function (self $productivityLog): void {
            $productivityLog->computeDuration();
        });

        static::updated(function (self $productivityLog): void {
            if ($productivityLog->wasChanged('finished_at') || $productivityLog->wasChanged('loss_type')) {
                if ($workCenter = $productivityLog->workCenter) {
                    $workCenter->computeWorkingState();

                    $workCenter->save();
                }
            }

            if ($productivityLog->wasChanged('duration')) {
                $productivityLog->workOrder->computeDuration();

                $productivityLog->workOrder->save();

            }
        });

        static::deleted(function (self $productivityLog): void {
            $productivityLog->workOrder->computeDuration();

            $productivityLog->workOrder->save();
        });
    }

    public function computeDuration()
    {
        if ($this->started_at && $this->finished_at) {
            $start = $this->started_at->copy()->setMicrosecond(0);

            $end = $this->finished_at->copy()->setMicrosecond(0);

            $this->duration = $this->loss->convertToDuration(
                $start,
                $end,
                $this->workCenter
            );
        } else {
            $this->duration = 0.0;
        }
    }

    public function stop(): void
    {
        $this->update(['finished_at' => now()]);

        $overrun = $this->splitOffOverrun();

        if ($overrun->isEmpty()) {
            return;
        }

        $overrun->each->update(['loss_id' => $this->underperformanceLoss()->id]);
    }

    protected function splitOffOverrun(): Collection
    {
        $overrunMinutes = $this->workOrder->duration - $this->workOrder->expected_duration;

        if ($overrunMinutes <= 0) {
            return collect();
        }

        $productiveUntil = Carbon::parse($this->finished_at)->subMinutes($overrunMinutes);

        if ($productiveUntil <= Carbon::parse($this->started_at)) {
            return collect([$this]);
        }

        $overrunLog = $this->replicate();

        $overrunLog->started_at = $productiveUntil;

        $overrunLog->save();

        $this->update(['finished_at' => $productiveUntil]);

        return collect([$overrunLog]);
    }

    protected function underperformanceLoss(): WorkCenterProductivityLoss
    {
        $loss = WorkCenterProductivityLoss::where('loss_type', 'performance')->first();

        if (! $loss) {
            throw new \Exception(__('manufacturing::system.work-center-productivity-log.no-performance-productivity-loss'));
        }

        return $loss;
    }
}
