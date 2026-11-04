<?php

namespace Webkul\Asset\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Database\Factories\AssetMovementFactory;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetSourceType;
use Webkul\Asset\Enums\MovementType;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class AssetMovement extends Model
{
    use BelongsToCompany, ChecksCompanyConsistency, HasFactory;

    public static bool $allowCleanupDelete = false;

    protected $table = 'asset_movements';

    protected $fillable = [
        'company_id',
        'asset_id',
        'type',
        'quantity',
        'source_type',
        'reference_type',
        'reference_id',
        'from_warehouse_id',
        'from_employee_id',
        'from_site_id',
        'to_warehouse_id',
        'to_employee_id',
        'to_site_id',
        'condition',
        'occurred_at',
        'expected_return_at',
        'performed_by_user_id',
        'note',
        'attachments',
    ];

    protected $casts = [
        'type'               => MovementType::class,
        'quantity'           => 'decimal:4',
        'source_type'        => AssetSourceType::class,
        'condition'          => AssetCondition::class,
        'occurred_at'        => 'datetime',
        'expected_return_at' => 'date',
        'attachments'        => 'array',
    ];

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw ValidationException::withMessages([
                'movement' => __('assets::services/asset-custody.movement-immutable'),
            ]);
        });

        static::deleting(function (self $movement): void {
            if (static::$allowCleanupDelete) {
                return;
            }

            throw ValidationException::withMessages([
                'movement' => __('assets::services/asset-custody.movement-immutable'),
            ]);
        });
    }

    public function getFromDisplayAttribute(): string
    {
        if ($this->type === MovementType::Registered) {
            return $this->source_type?->getLabel() ?? '—';
        }

        if (filled($this->from_employee_id) || filled($this->from_site_id)) {
            $employee = $this->fromEmployee?->name;
            $site = $this->fromSite?->full_name ?? $this->fromSite?->name;

            if ($employee && $site) {
                return "{$employee} — {$site}";
            }

            return $employee ?: ($site ?: '—');
        }

        if (filled($this->from_warehouse_id)) {
            return $this->fromWarehouse?->name ?? '—';
        }

        return $this->source_type?->getLabel() ?? '—';
    }

    public function getToDisplayAttribute(): string
    {
        if (filled($this->to_employee_id) || filled($this->to_site_id)) {
            $employee = $this->toEmployee?->name;
            $site = $this->toSite?->full_name ?? $this->toSite?->name;

            if ($employee && $site) {
                return "{$employee} — {$site}";
            }

            return $employee ?: ($site ?: '—');
        }

        if (filled($this->to_warehouse_id)) {
            return $this->toWarehouse?->name ?? '—';
        }

        if (in_array($this->type, [MovementType::Scrapped, MovementType::Lost], true)) {
            return $this->type->getLabel();
        }

        return '—';
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class)->withTrashed();
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(AssetWarehouse::class, 'from_warehouse_id')->withTrashed();
    }

    public function fromEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'from_employee_id')->withTrashed();
    }

    public function fromSite(): BelongsTo
    {
        return $this->belongsTo(AssetSite::class, 'from_site_id')->withTrashed();
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(AssetWarehouse::class, 'to_warehouse_id')->withTrashed();
    }

    public function toEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'to_employee_id')->withTrashed();
    }

    public function toSite(): BelongsTo
    {
        return $this->belongsTo(AssetSite::class, 'to_site_id')->withTrashed();
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    public function companyConsistentFields(): array
    {
        return [
            'from_warehouse_id' => AssetWarehouse::class,
            'from_employee_id'  => Employee::class,
            'from_site_id'      => AssetSite::class,
            'to_warehouse_id'   => AssetWarehouse::class,
            'to_employee_id'    => Employee::class,
            'to_site_id'        => AssetSite::class,
        ];
    }

    protected static function newFactory(): AssetMovementFactory
    {
        return AssetMovementFactory::new();
    }
}
