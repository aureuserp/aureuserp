<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Inventory\Database\Factories\OperationFactory;
use Webkul\Inventory\Enums;
use Webkul\Partner\Models\Partner;
use Webkul\Purchase\Models\Order as PurchaseOrder;
use Webkul\Sale\Models\Order as SaleOrder;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Operation extends Model
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'inventories_operations';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'origin',
        'move_type',
        'state',
        'is_favorite',
        'description',
        'has_deadline_issue',
        'is_printed',
        'is_locked',
        'deadline',
        'scheduled_at',
        'closed_at',
        'user_id',
        'owner_id',
        'operation_type_id',
        'source_location_id',
        'destination_location_id',
        'back_order_id',
        'return_id',
        'partner_id',
        'company_id',
        'creator_id',
        'sale_order_id',
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected $casts = [
        'state'              => Enums\OperationState::class,
        'move_type'          => Enums\MoveType::class,
        'is_favorite'        => 'boolean',
        'has_deadline_issue' => 'boolean',
        'is_printed'         => 'boolean',
        'is_locked'          => 'boolean',
        'deadline'           => 'datetime',
        'scheduled_at'       => 'datetime',
        'closed_at'          => 'datetime',
    ];

    protected array $logAttributes = [
        'name',
        'origin',
        'move_type',
        'state',
        'is_favorite',
        'description',
        'has_deadline_issue',
        'is_printed',
        'is_locked',
        'deadline',
        'scheduled_at',
        'closed_at',
        'user.name'                     => 'User',
        'owner.name'                    => 'Owner',
        'operationType.name'            => 'Operation Type',
        'sourceLocation.full_name'      => 'Source Location',
        'destinationLocation.full_name' => 'Destination Location',
        'backOrder.name'                => 'Back Order',
        'return.name'                   => 'Return',
        'partner.name'                  => 'Partner',
        'company.name'                  => 'Company',
        'creator.name'                  => 'Creator',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function operationType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class)->withTrashed();
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function backOrderOf(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function returnOf(): BelongsTo
    {
        return $this->belongsTo(self::class);
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

    public function moves(): HasMany
    {
        return $this->hasMany(Move::class, 'operation_id');
    }

    public function moveLines(): HasMany
    {
        return $this->hasMany(MoveLine::class, 'operation_id');
    }

    public function packages(): HasManyThrough
    {
        return $this->hasManyThrough(Package::class, MoveLine::class, 'operation_id', 'id', 'id', 'result_package_id');
    }

    public function purchaseOrders(): BelongsToMany
    {
        return $this->belongsToMany(PurchaseOrder::class, 'purchases_order_operations', 'inventory_operation_id', 'purchase_order_id');
    }

    public function saleOrder(): BelongsTo
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    /**
     * Bootstrap any application services.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($operation) {
            $operation->updateName();
        });

        static::created(function ($operation) {
            $operation->update(['name' => $operation->name]);
        });

        static::updated(function ($operation) {
            if ($operation->wasChanged('operation_type_id')) {
                $operation->updateChildrenNames();
            }
        });
    }

    /**
     * Update the full name without triggering additional events
     */
    public function updateName()
    {
        if (! $this->operationType->warehouse) {
            $this->name = $this->operationType->sequence_code.'/'.$this->id;
        } else {
            $this->name = $this->operationType->warehouse->code.'/'.$this->operationType->sequence_code.'/'.$this->id;
        }
    }

    public function updateChildrenNames(): void
    {
        foreach ($this->moves as $move) {
            $move->update(['name' => $this->name]);
        }

        foreach ($this->moveLines as $moveLine) {
            $moveLine->update(['name' => $this->name]);
        }
    }

    protected static function newFactory(): OperationFactory
    {
        return OperationFactory::new();
    }
}
