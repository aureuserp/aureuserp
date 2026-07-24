<?php

namespace Webkul\Maintenance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Maintenance\Database\Factories\EquipmentCategoryFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class EquipmentCategory extends Model
{
    use HasFactory;

    protected $table = 'maintenance_equipment_categories';

    protected $fillable = [
        'name',
        'note',
        'creator_id',
        'technician_user_id',
        'company_id',
    ];

    public function getModelTitle(): string
    {
        return __('maintenance::models/equipment-category.title');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class, 'category_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'category_id');
    }

    protected static function newFactory(): EquipmentCategoryFactory
    {
        return EquipmentCategoryFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $category): void {
            $authUser = Auth::user();

            $category->creator_id ??= $authUser?->id;
            $category->company_id ??= $authUser?->default_company_id;
        });
    }
}
