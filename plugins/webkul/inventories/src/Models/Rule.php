<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Inventory\Database\Factories\RuleFactory;
use Webkul\Inventory\Enums\GroupPropagation;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Enums\RuleAuto;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Rule extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'inventories_rules';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'sort',
        'name',
        'route_sort',
        'delay',
        'group_propagation_option',
        'action',
        'procure_method',
        'auto',
        'push_domain',
        'location_dest_from_rule',
        'propagate_cancel',
        'propagate_carrier',
        'source_location_id',
        'destination_location_id',
        'route_id',
        'operation_type_id',
        'partner_address_id',
        'warehouse_id',
        'propagate_warehouse_id',
        'company_id',
        'creator_id',
        'deleted_at',
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected $casts = [
        'action'                   => RuleAction::class,
        'group_propagation_option' => GroupPropagation::class,
        'auto'                     => RuleAuto::class,
        'procure_method'           => ProcureMethod::class,
        'location_dest_from_rule'  => 'boolean',
        'propagate_cancel'         => 'boolean',
        'propagate_carrier'        => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function operationType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function propagateWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function partnerAddress(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): RuleFactory
    {
        return RuleFactory::new();
    }
}
