<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Inventory\Database\Factories\PackageTypeFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class PackageType extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'inventories_package_types';

    protected $fillable = [
        'name',
        'sort',
        'barcode',
        'height',
        'width',
        'length',
        'base_weight',
        'max_weight',
        'shipper_package_code',
        'package_carrier_type',
        'company_id',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): PackageTypeFactory
    {
        return PackageTypeFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($packageType) {
            $authUser = Auth::user();

            $packageType->creator_id ??= $authUser->id;

            $packageType->company_id ??= $authUser?->default_company_id;
        });
    }
}
