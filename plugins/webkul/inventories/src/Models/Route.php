<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Inventory\Database\Factories\RouteFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Route extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'inventories_routes';

    protected $fillable = [
        'sort',
        'name',
        'product_selectable',
        'product_category_selectable',
        'warehouse_selectable',
        'packaging_selectable',
        'supplied_warehouse_id',
        'supplier_warehouse_id',
        'company_id',
        'creator_id',
        'deleted_at',
    ];

    protected $casts = [
        'product_selectable'          => 'boolean',
        'product_category_selectable' => 'boolean',
        'warehouse_selectable'        => 'boolean',
        'packaging_selectable'        => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function suppliedWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplierWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'inventories_route_warehouses');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function packagings(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'inventories_route_packagings', 'route_id', 'packaging_id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }

    protected static function newFactory(): RouteFactory
    {
        return RouteFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($route) {
            $authUser = Auth::user();

            $route->creator_id ??= $authUser->id;

            $route->company_id ??= $authUser?->default_company_id;
        });
    }
}
