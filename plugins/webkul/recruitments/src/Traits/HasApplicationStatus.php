<?php

namespace Webkul\Recruitment\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Recruitment\Enums\ApplicationStatus;
use Webkul\Recruitment\Models\Stage;

trait HasApplicationStatus
{
    public function getApplicationStatusAttribute(): ApplicationStatus
    {
        if (
            $this->trashed()
            || ! $this->is_active
        ) {
            return ApplicationStatus::ARCHIVED;
        }

        if ($this->refuse_reason_id) {
            return ApplicationStatus::REFUSED;
        }

        if ($this->date_closed) {
            return ApplicationStatus::HIRED;
        }

        return ApplicationStatus::ONGOING;
    }

    public function scopeStatus(Builder $query, string|array $status): Builder
    {
        $statuses = is_array($status) ? $status : [$status];

        return $query->where(function ($query) use ($statuses) {
            foreach ($statuses as $status) {
                match ($status) {
                    ApplicationStatus::REFUSED->value  => $query->orWhere('refuse_reason_id', '!=', null),
                    ApplicationStatus::HIRED->value    => $query->orWhere('date_closed', '!=', null),
                    ApplicationStatus::ARCHIVED->value => $query->onlyTrashed(),
                    ApplicationStatus::ONGOING->value  => $query->orWhere(function ($q) {
                        $q->whereNull('refuse_reason_id')
                            ->whereNull('date_closed');
                    }),
                    default => null
                };
            }
        });
    }

    public function updateStatus(string $status, ?array $attributes = []): bool
    {
        return DB::transaction(function () use ($status, $attributes) {
            $newStatus = ApplicationStatus::from($status);

            if ($this->trashed() && $newStatus !== ApplicationStatus::ARCHIVED) {
                $this->restore();
            }

            $updates = match ($newStatus) {
                ApplicationStatus::REFUSED => [
                    'refuse_reason_id' => $attributes['refuse_reason_id'] ?? null,
                    'refuse_date'      => now(),
                    'date_closed'      => null,
                    'is_active'        => false,
                ],
                ApplicationStatus::HIRED => [
                    'date_closed'      => now(),
                    'refuse_reason_id' => null,
                    'refuse_date'      => null,
                ],
                ApplicationStatus::ONGOING => [
                    'date_closed'      => null,
                    'refuse_reason_id' => null,
                    'refuse_date'      => null,
                    'is_active'        => true,
                    'stage_id'         => Stage::where('is_default', true)->first()->id ?? null,
                ],
                ApplicationStatus::ARCHIVED => [
                    'date_closed'      => null,
                    'refuse_reason_id' => null,
                    'refuse_date'      => null,
                    'is_active'        => false,
                    'deleted_at'       => now(),
                ],
            };

            $updated = $this->update($updates);

            return $updated;
        });
    }

    public function scopeWithArchived(Builder $query): Builder
    {
        return $query->withTrashed();
    }

    public function scopeOnlyArchived(Builder $query): Builder
    {
        return $query->onlyTrashed();
    }
}
