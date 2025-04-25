<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Inventory\Database\Factories\MoveFactory;
use Webkul\Inventory\Enums;
use Webkul\Partner\Models\Partner;
use Webkul\Purchase\Models\OrderLine as PurchaseOrderLine;
use Webkul\Sale\Models\OrderLine as SaleOrderLine;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class Move extends Model
{
    use HasFactory;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'inventories_moves';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'state',
        'origin',
        'procure_method',
        'reference',
        'description_picking',
        'next_serial',
        'next_serial_count',
        'is_favorite',
        'product_qty',
        'product_uom_qty',
        'quantity',
        'is_picked',
        'is_scraped',
        'is_inventory',
        'is_refund',
        'deadline',
        'reservation_date',
        'scheduled_at',
        'product_id',
        'uom_id',
        'source_location_id',
        'destination_location_id',
        'final_location_id',
        'partner_id',
        'operation_id',
        'rule_id',
        'operation_type_id',
        'origin_returned_move_id',
        'restrict_partner_id',
        'warehouse_id',
        'product_packaging_id',
        'scrap_id',
        'company_id',
        'creator_id',
        'purchase_order_line_id',
        'sale_order_line_id',
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected $casts = [
        'state'            => Enums\MoveState::class,
        'is_favorite'      => 'boolean',
        'is_picked'        => 'boolean',
        'is_scraped'       => 'boolean',
        'is_inventory'     => 'boolean',
        'is_refund'        => 'boolean',
        'reservation_date' => 'date',
        'scheduled_at'     => 'datetime',
        'deadline'         => 'datetime',
        'alert_Date'       => 'datetime',
    ];

    /**
     * Determines if a stock move is a purchase return
     *
     * @return bool True if the move is a purchase return, false otherwise
     */
    public function isPurchaseReturn()
    {
        return $this->destinationLocation->type === Enums\LocationType::SUPPLIER
            || (
                $this->originReturnedMove
                && $this->destinationLocation->id === $this->destinationLocation->company->inter_company_location_id
            );
    }

    /**
     * Determines if a stock move is a purchase return
     *
     * @return bool True if the move is a purchase return, false otherwise
     */
    public function isDropshipped()
    {
        return (
            $this->sourceLocation->type === Enums\LocationType::SUPPLIER
            || ($this->sourceLocation->type === Enums\LocationType::TRANSIT && ! $this->sourceLocation->company_id)
        )
            && (
                $this->destinationLocation->type === Enums\LocationType::CUSTOMER
                || ($this->destinationLocation->type === Enums\LocationType::TRANSIT && ! $this->destinationLocation->company_id)
            );
    }

    /**
     * Determines if a stock move is a purchase return
     *
     * @return bool True if the move is a purchase return, false otherwise
     */
    public function isDropshippedReturned()
    {
        return (
            $this->sourceLocation->type === Enums\LocationType::CUSTOMER
            || ($this->sourceLocation->type === Enums\LocationType::TRANSIT && ! $this->sourceLocation->company_id)
        )
            && (
                $this->destinationLocation->type === Enums\LocationType::SUPPLIER
                || ($this->destinationLocation->type === Enums\LocationType::TRANSIT && ! $this->destinationLocation->company_id)
            );
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

    public function finalLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function scrap(): BelongsTo
    {
        return $this->belongsTo(Scrap::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    public function operationType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class);
    }

    public function originReturnedMove(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function restrictPartner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function packageLevel(): BelongsTo
    {
        return $this->belongsTo(PackageLevel::class);
    }

    public function productPackaging(): BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(MoveLine::class);
    }

    public function moveDestinations(): BelongsToMany
    {
        return $this->belongsToMany(Move::class, 'inventories_move_destinations', 'origin_move_id', 'destination_move_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shouldBypassReservation(): bool
    {
        return $this->sourceLocation->shouldBypassReservation() || ! $this->product->is_storable;
    }

    public function purchaseOrderLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderLine::class, 'purchase_order_line_id');
    }

    public function saleOrderLine(): BelongsTo
    {
        return $this->belongsTo(SaleOrderLine::class, 'sale_order_line_id');
    }

    protected static function newFactory(): MoveFactory
    {
        return MoveFactory::new();
    }
}
