<?php

namespace Webkul\Maintenance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Maintenance\Database\Factories\EquipmentFactory;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'maintenance_equipments';

    protected $fillable = [
        'partner_ref',
        'location',
        'model',
        'serial_no',
        'effective_date',
        'warranty_date',
        'assigned_at',
        'scraped_at',
        'name',
        'note',
        'cost',
        'maintenance_count',
        'maintenance_open_count',
        'expected_mtbf',
        'category_id',
        'partner_id',
        'owner_user_id',
        'maintenance_team_id',
        'technician_user_id',
        'company_id',
        'creator_id',
        'deleted_at',
    ];

    protected $casts = [
        'effective_date'          => 'date',
        'warranty_date'           => 'date',
        'assigned_at'             => 'date',
        'scraped_at'              => 'date',
        'cost'                    => 'float',
        'maintenance_count'       => 'integer',
        'maintenance_open_count'  => 'integer',
        'expected_mtbf'           => 'integer',
    ];

    public function getModelTitle(): string
    {
        return __('maintenance::models/equipment.title');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'category_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'maintenance_team_id')->withTrashed();
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'equipment_id');
    }

    protected static function newFactory(): EquipmentFactory
    {
        return EquipmentFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $equipment): void {
            $authUser = Auth::user();

            $equipment->creator_id ??= $authUser?->id;
            $equipment->company_id ??= $authUser?->default_company_id;
            $equipment->effective_date ??= now()->toDateString();
            $equipment->maintenance_count ??= 0;
            $equipment->maintenance_open_count ??= 0;
        });
    }
}
