<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Inventory\Database\Factories\MoveLineFactory;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class MoveLine extends Model
{
    use HasFactory;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'inventories_move_lines';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'lot_name',
        'state',
        'reference',
        'picking_description',
        'qty',
        'uom_qty',
        'is_picked',
        'scheduled_at',
        'move_id',
        'operation_id',
        'product_id',
        'uom_id',
        'package_id',
        'result_package_id',
        'package_level_id',
        'lot_id',
        'partner_id',
        'source_location_id',
        'destination_location_id',
        'company_id',
        'creator_id',
    ];

    /**
     * Table casts.
     *
     * @var array
     */
    protected $casts = [
        'state'             => MoveState::class,
        'is_picked'         => 'boolean',
        'scheduled_at'      => 'datetime',
    ];

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function resultPackage(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function packageLevel(): BelongsTo
    {
        return $this->belongsTo(PackageLevel::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function partner(): BelongsTo
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

    protected static function newFactory(): MoveLineFactory
    {
        return MoveLineFactory::new();
    }
}
