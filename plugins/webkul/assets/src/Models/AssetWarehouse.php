<?php

namespace Webkul\Asset\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Database\Factories\AssetWarehouseFactory;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Employee;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;
use Webkul\Support\Support\CompanyConsistencyGuard;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class AssetWarehouse extends Model
{
    use BelongsToCompany;
    use ChecksCompanyConsistency, HasChatter, HasCustomFields, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'asset_warehouses';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'custodian_employee_id',
        'is_active',
        'note',
        'creator_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $warehouse): void {
            if (blank($warehouse->company_id)) {
                $warehouse->company_id = app(CompanyContext::class)->currentId();
            }

            if (blank($warehouse->creator_id)) {
                $warehouse->creator_id = Auth::id();
            }

            if (filled($warehouse->custodian_employee_id)) {
                CompanyConsistencyGuard::assert(
                    $warehouse->company_id,
                    [['custodian_employee_id' => $warehouse->custodian_employee_id]],
                    ['custodian_employee_id' => Employee::class],
                );
            }
        });

        static::updating(function (self $warehouse): void {
            if ($warehouse->isDirty('custodian_employee_id') && filled($warehouse->custodian_employee_id)) {
                CompanyConsistencyGuard::assert(
                    $warehouse->company_id,
                    [['custodian_employee_id' => $warehouse->custodian_employee_id]],
                    ['custodian_employee_id' => Employee::class],
                );
            }

            if ($warehouse->isDirty('company_id')) {
                $isReferenced = Asset::withoutGlobalScopes()->withTrashed()->where('warehouse_id', $warehouse->id)->exists()
                    || AssetMovement::withoutGlobalScopes()->where(function ($q) use ($warehouse) {
                        $q->where('from_warehouse_id', $warehouse->id)
                            ->orWhere('to_warehouse_id', $warehouse->id);
                    })->exists();

                if ($isReferenced) {
                    throw ValidationException::withMessages([
                        'company_id' => __('assets::models/asset-warehouse.company.immutable'),
                    ]);
                }
            }

            if ($warehouse->isDirty('is_active') && ! $warehouse->is_active) {
                if (Asset::withoutGlobalScopes()->where('warehouse_id', $warehouse->id)->exists()) {
                    throw ValidationException::withMessages([
                        'is_active' => __('assets::models/asset-warehouse.deactivation.has-assets'),
                    ]);
                }
            }
        });

        static::deleting(function (self $warehouse): void {
            if ($warehouse->isForceDeleting()) {
                $hasAssets = Asset::withoutGlobalScopes()->withTrashed()->where('warehouse_id', $warehouse->id)->exists();
                $hasMovements = AssetMovement::withoutGlobalScopes()->where(function ($q) use ($warehouse) {
                    $q->where('from_warehouse_id', $warehouse->id)
                        ->orWhere('to_warehouse_id', $warehouse->id);
                })->exists();

                if ($hasAssets || $hasMovements) {
                    throw ValidationException::withMessages([
                        'warehouse' => __('assets::models/asset-warehouse.deletion.referenced'),
                    ]);
                }

                return;
            }

            if (Asset::withoutGlobalScopes()->where('warehouse_id', $warehouse->id)->exists()) {
                throw ValidationException::withMessages([
                    'warehouse' => __('assets::models/asset-warehouse.deletion.referenced'),
                ]);
            }
        });
    }

    public function getModelTitle(): string
    {
        return __('assets::models/asset-warehouse.title');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function custodian(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'custodian_employee_id')->withTrashed();
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'warehouse_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function companyConsistentFields(): array
    {
        return [
            'custodian_employee_id' => Employee::class,
        ];
    }

    protected static function newFactory(): AssetWarehouseFactory
    {
        return AssetWarehouseFactory::new();
    }
}
