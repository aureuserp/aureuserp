<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Inventory\Database\Factories\OperationFactory;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Partner\Models\Partner;
use Webkul\Purchase\Models\Order as PurchaseOrder;
use Webkul\Sale\Models\Order as SaleOrder;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasPermissionScope;
use Webkul\Support\Models\Company;
use Throwable;

class Operation extends Model
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, HasPermissionScope;

    public const ACTIVITY_PLAN_PLUGIN = 'inventories';

    protected $table = 'inventories_operations';

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
        'procurement_group_id',
        'sale_order_id',
    ];

    protected $casts = [
        'state'              => OperationState::class,
        'move_type'          => MoveType::class,
        'is_favorite'        => 'boolean',
        'has_deadline_issue' => 'boolean',
        'is_printed'         => 'boolean',
        'is_locked'          => 'boolean',
        'deadline'           => 'datetime',
        'scheduled_at'       => 'datetime',
        'closed_at'          => 'datetime',
    ];

    protected array $context = [];

    public function setContext(array $context)
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    public function getModelTitle(): string
    {
        return __('inventories::models/operation.title');
    }

    public function getChatterResourceUrl(): string
    {
        try {
            return OperationResource::getUrl('view', ['record' => $this], panel: 'admin');
        } catch (Throwable $e) {
            return '';
        }
    }

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'                          => __('inventories::models/operation.log-attributes.name'),
            'origin'                        => __('inventories::models/operation.log-attributes.origin'),
            'move_type'                     => __('inventories::models/operation.log-attributes.move_type'),
            'state'                         => __('inventories::models/operation.log-attributes.state'),
            'is_favorite'                   => __('inventories::models/operation.log-attributes.is_favorite'),
            'description'                   => __('inventories::models/operation.log-attributes.description'),
            'has_deadline_issue'            => __('inventories::models/operation.log-attributes.has_deadline_issue'),
            'is_printed'                    => __('inventories::models/operation.log-attributes.is_printed'),
            'is_locked'                     => __('inventories::models/operation.log-attributes.is_locked'),
            'deadline'                      => __('inventories::models/operation.log-attributes.deadline'),
            'scheduled_at'                  => __('inventories::models/operation.log-attributes.scheduled_at'),
            'closed_at'                     => __('inventories::models/operation.log-attributes.closed_at'),
            'user.name'                     => __('inventories::models/operation.log-attributes.user'),
            'owner.name'                    => __('inventories::models/operation.log-attributes.owner'),
            'operationType.name'            => __('inventories::models/operation.log-attributes.operation-type'),
            'sourceLocation.full_name'      => __('inventories::models/operation.log-attributes.source-location'),
            'destinationLocation.full_name' => __('inventories::models/operation.log-attributes.destination-location'),
            'backOrder.name'                => __('inventories::models/operation.log-attributes.back-order'),
            'return.name'                   => __('inventories::models/operation.log-attributes.return'),
            'partner.name'                  => __('inventories::models/operation.log-attributes.partner'),
            'company.name'                  => __('inventories::models/operation.log-attributes.company'),
            'creator.name'                  => __('inventories::models/operation.log-attributes.creator'),
        ];
    }

    public function return(): BelongsTo
    {
        return $this->belongsTo(self::class, 'return_id');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(self::class, 'return_id');
    }

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

    public function packageLevels(): HasMany
    {
        return $this->hasMany(PackageLevel::class, 'operation_id');
    }

    public function procurementGroup(): BelongsTo
    {
        return $this->belongsTo(ProcurementGroup::class, 'procurement_group_id');
    }

    public function purchaseOrders(): BelongsToMany
    {
        return $this->belongsToMany(PurchaseOrder::class, 'purchases_order_operations', 'inventory_operation_id', 'purchase_order_id');
    }

    public function saleOrder(): BelongsTo
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    public function nextTransfersQuery()
    {
        $returnIds = $this->returns()->pluck('id');

        $nextTransferIds = $this->moves()
            ->with(['moveDestinations:id,operation_id'])
            ->get()
            ->flatMap(fn (Move $move) => $move->moveDestinations->pluck('operation_id'))
            ->filter()
            ->unique()
            ->values();

        $query = self::query()
            ->whereIn('id', $nextTransferIds)
            ->distinct();

        if ($returnIds->isNotEmpty()) {
            $query->whereNotIn('id', $returnIds);
        }

        return $query;
    }

    public function nextTransfers()
    {
        return $this->nextTransfersQuery()->get();
    }

    public function getShowNextOperationsAttribute(): bool
    {
        return $this->nextTransfersQuery()->exists();
    }

    protected static function newFactory(): OperationFactory
    {
        return OperationFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($operation) {
            $operation->creator_id ??= Auth::id();

            $operation->user_id ??= Auth::id();

            $operation->state ??= OperationState::DRAFT;
        });

        static::created(function ($operation) {
            $operation->update(['name' => $operation->name]);
        });

        static::updated(function ($operation) {
            if ($operation->wasChanged('operation_type_id')) {
                $operation->updateChildrenNames();
            }

            if (
                $operation->wasChanged('source_location_id')
                || $operation->wasChanged('destination_location_id')
            ) {
                $operation->moves->each(function($move) use ($operation) {
                    $move->source_location_id = $operation->source_location_id ?? $operation->operationType?->source_location_id;

                    $move->destination_location_id = $operation->destination_location_id ?? $operation->operationType?->destination_location_id;

                    $move->save();
                });
            }
        });

        static::saving(function ($operation) {
            $operation->updateName();
        });

        static::saved(function ($operation) {
            $operation->computeDeadline();

            $operation->computeScheduledAt();

            $operation->saveQuietly();
        });
    }

    public function autoConfirm()
    {
        if (in_array($this->state, [OperationState::DONE, OperationState::CANCELED])) {
            return;
        }

        if ($this->moves->isEmpty() && $this->packageLevels->isEmpty()) {
            return;
        }

        if ($this->moves->some(fn ($move) => $move->additional)) {
            InventoryFacade::confirmTransfer($this);
        }

        $movesToConfirm = $this->moves->filter(fn ($move) => $move->state === MoveState::DRAFT && $move->quantity);

        InventoryFacade::confirmMoves($movesToConfirm);

    }

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

    public function computeDeadline(): void
    {
        $deadlines = $this->moves->filter(fn ($m) => $m->deadline)->pluck('deadline');

        if ($deadlines->isEmpty()) {
            $this->deadline = null;

            return;
        }

        $this->deadline = $this->move_type === 'direct'
            ? $deadlines->min()
            : $deadlines->max();
    }

    public function computeScheduledAt(): void
    {
        $movesDates = $this->moves
            ->filter(fn ($move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]))
            ->pluck('scheduled_at');

        $defaultDate = $this->scheduled_at ?: now();

        if ($this->move_type === 'direct') {
            $this->scheduled_at = $movesDates->min() ?? $defaultDate;
        } else {
            $this->scheduled_at = $movesDates->max() ?? $defaultDate;
        }
    }

    public function computeState()
    {
        if (in_array($this->state, [OperationState::DONE, OperationState::CANCELED])) {
            return;
        }

        if ($this->moves->isEmpty() || $this->moves->some(fn ($move) => $move->state === MoveState::DRAFT)) {
            $this->state = OperationState::DRAFT;
        } elseif ($this->moves->every(fn ($move) => $move->state === MoveState::CANCELED)) {
            $this->state = OperationState::CANCELED;
        } elseif ($this->moves->every(fn ($move) => in_array($move->state, [MoveState::CANCELED, MoveState::DONE]))) {
            $allDoneAreScraped = $this->moves->every(fn ($move) => $move->state === MoveState::DONE ? $move->is_scraped : true);

            $anyCancelNotScrapped = $this->moves->some(fn ($move) => $move->state === MoveState::CANCELED && ! $move->is_scraped);

            $this->state = ($allDoneAreScraped && $anyCancelNotScrapped)
                ? OperationState::CANCELED
                : OperationState::DONE;
        } elseif ($this->moves->every(fn ($move) => $move->state === MoveState::CONFIRMED)) {
            $this->state = OperationState::CONFIRMED;
        } elseif (
            $this->sourceLocation->shouldBypassReservation() &&
            $this->moves->every(fn ($move) => $move->procure_method === ProcureMethod::MAKE_TO_STOCK)
        ) {
            $this->state = OperationState::ASSIGNED;
        } else {
            $relevantMoveState = InventoryFacade::getRelevantStateAmongMoves($this->moves);

            $this->state = $relevantMoveState === MoveState::PARTIALLY_ASSIGNED
                ? OperationState::ASSIGNED
                : OperationState::from($relevantMoveState->value);
        }
    }

    public static function getImpactedOperations($moves)
    {
        $impactedOperations = collect();

        $exploredMoves = collect();

        $explore = function ($movesToExplore) use (&$explore, &$impactedOperations, &$exploredMoves) {
            foreach ($movesToExplore as $move) {
                if (! $exploredMoves->contains('id', $move->id)) {
                    if ($move->operation_id) {
                        $impactedOperations->push($move->operation);
                    }

                    $exploredMoves->push($move);

                    $movesToExplore = $movesToExplore->merge($move->moveDestinations);
                }
            }

            $movesToExplore = $movesToExplore->filter(fn ($move) => ! $exploredMoves->contains('id', $move->id));

            if ($movesToExplore->isNotEmpty()) {
                $explore($movesToExplore);
            }
        };

        $explore($moves);

        return $impactedOperations->unique('id');
    }


    public function getEntirePackDestinationLocation($moveLines)
    {
        $destinationLocationIds = $moveLines
            ->pluck('destination_location_id')
            ->unique()
            ->values();

        if ($destinationLocationIds->count() > 1) {
            return false;
        }

        return $destinationLocationIds->first();
    }

    public function checkMoveLinesMapQuant($moveLines, Package $package): mixed
    {
        return $package->checkMoveLinesMapQuant(
            $moveLines->filter(fn ($moveLine) => $moveLine->product->is_storable)
        );
    }

    public function checkMoveLinesMapQuantPackage(Package $package): mixed
    {
        return $this->checkMoveLinesMapQuant(
            $this->moveLines->filter(fn ($moveLine) => $moveLine->package_id === $package->id),
            $package
        );
    }
}
