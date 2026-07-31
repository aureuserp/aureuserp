<?php

namespace Webkul\Manufacturing\Models;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Manufacturing\Database\Factories\WorkCenterFactory;
use Webkul\Manufacturing\Enums\WorkCenterWorkingState;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class WorkCenter extends Model implements Sortable
{
    use BelongsToCompany;
    use HasCustomFields, HasFactory, SoftDeletes, SortableTrait;

    protected const SLOT_SEARCH_WINDOW_DAYS = 14;

    protected const SLOT_SEARCH_WINDOWS = 50;

    protected $table = 'manufacturing_work_centers';

    protected $fillable = [
        'sort',
        'color',
        'name',
        'code',
        'working_state',
        'note',
        'time_efficiency',
        'default_capacity',
        'costs_per_hour',
        'setup_time',
        'cleanup_time',
        'oee_target',
        'company_id',
        'calendar_id',
        'creator_id',
        'deleted_at',
    ];

    protected $casts = [
        'working_state'    => WorkCenterWorkingState::class,
        'time_efficiency'  => 'decimal:2',
        'costs_per_hour'   => 'decimal:4',
        'setup_time'       => 'decimal:4',
        'cleanup_time'     => 'decimal:4',
        'oee_target'       => 'decimal:2',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function getModelTitle(): string
    {
        return __('manufacturing::models/work-center.title');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class, 'calendar_id')->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class, 'work_center_id');
    }

    public function capacities(): HasMany
    {
        return $this->hasMany(WorkCenterCapacity::class, 'work_center_id');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'work_center_id');
    }

    public function productivityLogs(): HasMany
    {
        return $this->hasMany(WorkCenterProductivityLog::class, 'work_center_id');
    }

    public function alternativeWorkCenters(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'manufacturing_work_center_alternatives', 'work_center_id', 'alternative_work_center_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(WorkCenterTag::class, 'manufacturing_work_center_tag', 'work_center_id', 'tag_id');
    }

    public function capacityFor(?Product $product = null): float
    {
        $capacity = $product ? $this->capacityRuleFor($product) : null;

        return max((float) ($capacity?->capacity ?? $this->default_capacity ?? 1), 0.0001);
    }

    public function setupAndCleanupTime(?Product $product = null): float
    {
        $capacity = $product ? $this->capacityRuleFor($product) : null;

        return (float) ($capacity?->time_start ?? $this->setup_time ?? 0)
            + (float) ($capacity?->time_stop ?? $this->cleanup_time ?? 0);
    }

    protected function capacityRuleFor(Product $product): ?WorkCenterCapacity
    {
        return $this->capacities
            ->first(fn (WorkCenterCapacity $capacity): bool => (int) $capacity->product_id === (int) $product->getKey());
    }

    public function findFirstAvailableSlot(
        Carbon $startDatetime,
        float $duration,
        bool $forward = true,
        ?Collection $leavesToIgnore = null,
        array $extraLeavesSlots = []
    ): array {
        $blockers = collect($extraLeavesSlots)
            ->map(fn (array $slot) => [Carbon::parse($slot[0]), Carbon::parse($slot[1])]);

        $leaveFilter = $this->workOrderLeaveFilter($leavesToIgnore);

        $remaining = $duration;

        $anchor = null;

        for ($window = 0; $window < static::SLOT_SEARCH_WINDOWS; $window++) {
            [$windowStart, $windowStop] = $this->searchWindow($startDatetime, $window, $forward);

            $slot = $forward
                ? $this->scanForward($windowStart, $windowStop, $leaveFilter, $blockers, $duration, $remaining, $anchor)
                : $this->scanBackward($windowStart, $windowStop, $leaveFilter, $blockers, $duration, $remaining, $anchor);

            if ($slot['found']) {
                return $slot['found'];
            }

            $remaining = $slot['remaining'];

            $anchor = $slot['anchor'];

            if (! $forward && $windowStart->lte(now())) {
                break;
            }
        }

        return [false, 'No available slot 700 days after the planned start'];
    }

    protected function workOrderLeaveFilter(?Collection $leavesToIgnore): Closure
    {
        $ignoredIds = $leavesToIgnore?->pluck('id')->all() ?? [];

        return fn ($query) => $query
            ->where('time_type', 'other')
            ->when($ignoredIds, fn ($q) => $q->whereNotIn('id', $ignoredIds));
    }

    protected function searchWindow(Carbon $startDatetime, int $window, bool $forward): array
    {
        $span = static::SLOT_SEARCH_WINDOW_DAYS;

        if ($forward) {
            $start = $startDatetime->clone()->addDays($span * $window);

            return [$start, $start->clone()->addDays($span)];
        }

        $stop = $startDatetime->clone()->subDays($span * $window);

        return [$stop->clone()->subDays($span), $stop];
    }

    protected function workingIntervals(Carbon $from, Carbon $to): Collection
    {
        return collect($this->calendar->getWorkIntervalsBatch($from, $to, $this)[$this->id] ?? []);
    }

    protected function blockedIntervals(Carbon $from, Carbon $to, Closure $leaveFilter, Collection $extraBlockers): Collection
    {
        return collect($this->calendar->getLeaveIntervalsBatch($from, $to, $this, $leaveFilter)[$this->id] ?? [])
            ->merge($extraBlockers);
    }

    protected function firstOverlap(Collection $blockers, Carbon $from, Carbon $to): ?array
    {
        foreach ($blockers as [$blockedFrom, $blockedTo]) {
            if ($from->lt($blockedTo) && $to->gt($blockedFrom)) {
                return [$blockedFrom, $blockedTo];
            }
        }

        return null;
    }

    protected function scanForward(
        Carbon $windowStart,
        Carbon $windowStop,
        Closure $leaveFilter,
        Collection $extraBlockers,
        float $duration,
        float $remaining,
        ?Carbon $anchor
    ): array {
        $blockers = $this->blockedIntervals($windowStart, $windowStop, $leaveFilter, $extraBlockers);

        foreach ($this->workingIntervals($windowStart, $windowStop) as [$start, $stop]) {
            $anchor ??= $start;

            $available = $this->minutesBetween($start, $stop);

            while (true) {
                $candidateEnd = $start->clone()->addMinutes(min($remaining, $available));

                $overlap = $this->firstOverlap($blockers, $anchor, $candidateEnd);

                if (! $overlap) {
                    break;
                }

                $start = $overlap[1];

                $available = $this->minutesBetween($start, $stop);

                if ($available <= 0) {
                    $available = 0;

                    $anchor = null;

                    $remaining = $duration;

                    break;
                }

                $anchor = $start;
            }

            if (float_compare($available, $remaining, precisionDigits: 3) >= 0) {
                return ['found' => [$anchor, $start->clone()->addMinutes($remaining)]];
            }

            $remaining -= $available;
        }

        return ['found' => null, 'remaining' => $remaining, 'anchor' => $anchor];
    }

    protected function scanBackward(
        Carbon $windowStart,
        Carbon $windowStop,
        Closure $leaveFilter,
        Collection $extraBlockers,
        float $duration,
        float $remaining,
        ?Carbon $anchor
    ): array {
        $blockers = $this->blockedIntervals($windowStart, $windowStop, $leaveFilter, $extraBlockers);

        foreach ($this->workingIntervals($windowStart, $windowStop)->reverse() as [$start, $stop]) {
            $anchor ??= $stop;

            $available = $this->minutesBetween($start, $stop);

            while (true) {
                $candidateStart = $stop->clone()->subMinutes(min($remaining, $available));

                $overlap = $this->firstOverlap($blockers, $candidateStart, $anchor);

                if (! $overlap) {
                    break;
                }

                $stop = $overlap[0];

                $available = $this->minutesBetween($start, $stop);

                if ($available <= 0) {
                    $available = 0;

                    $anchor = null;

                    $remaining = $duration;

                    break;
                }

                $anchor = $stop;
            }

            if (float_compare($available, $remaining, precisionDigits: 3) >= 0) {
                return ['found' => [$stop->clone()->subMinutes($remaining), $anchor]];
            }

            $remaining -= $available;
        }

        return ['found' => null, 'remaining' => $remaining, 'anchor' => $anchor];
    }

    protected function minutesBetween(Carbon $start, Carbon $stop): float
    {
        return $start->diffInSeconds($stop) / 60;
    }

    protected static function newFactory(): WorkCenterFactory
    {
        return WorkCenterFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $workCenter): void {
            $authUser = Auth::user();

            $workCenter->creator_id ??= $authUser?->id;
            $workCenter->company_id ??= current_company_id();
            $workCenter->working_state ??= WorkCenterWorkingState::NORMAL;
            $workCenter->time_efficiency ??= 100;
            $workCenter->default_capacity ??= 1;
            $workCenter->costs_per_hour ??= 0;
            $workCenter->setup_time ??= 0;
            $workCenter->cleanup_time ??= 0;
            $workCenter->oee_target ??= 90;
        });
    }

    public function computeWorkingState(): void
    {
        $productivityLog = $this->productivityLogs()
            ->whereNull('finished_at')
            ->first();

        if (! $productivityLog) {
            $this->working_state = WorkCenterWorkingState::NORMAL;
        } elseif (in_array($productivityLog->loss_type, ['productive', 'performance'])) {
            $this->working_state = WorkCenterWorkingState::DONE;
        } else {
            $this->working_state = WorkCenterWorkingState::BLOCKED;
        }
    }

    public function unblock()
    {
        if ($this->working_state !== WorkCenterWorkingState::BLOCKED) {
            throw new \Exception(__('manufacturing::system.work-center.already-unblocked'));
        }

        $this->productivityLogs()->whereNull('finished_at')->update(['finished_at' => now()]);
    }

    public function getWorkDaysDataBatch($startDate, $endDate, $computeLeaves = true, $calendar = null, ?Closure $filter = null)
    {
        if (! $calendar) {
            $calendar = $this->calendar;
        }

        if (! $calendar) {
            return [
                $this->id => [
                    'hours' => 0,
                    'days'  => 0,
                ],
            ];
        }

        if ($computeLeaves) {
            $internals = $calendar->getWorkIntervalsBatch($startDate, $endDate, [$this], $filter)[$this->id];
        } else {
            $internals = $calendar->getAttendanceIntervalsBatch($startDate, $endDate, [$this])[$this->id];
        }

        return $calendar->getAttendanceIntervalsDaysData($internals);
    }
}
