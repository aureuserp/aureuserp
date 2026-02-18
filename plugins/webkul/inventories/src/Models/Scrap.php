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
use Webkul\Inventory\Database\Factories\ScrapFactory;
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class Scrap extends Model
{
    use HasChatter, HasFactory, HasLogActivity;

    protected $table = 'inventories_scraps';

    protected $fillable = [
        'name',
        'origin',
        'state',
        'qty',
        'should_replenish',
        'closed_at',
        'product_id',
        'uom_id',
        'lot_id',
        'package_id',
        'partner_id',
        'operation_id',
        'source_location_id',
        'destination_location_id',
        'company_id',
        'creator_id',
    ];

    public function getModelTitle(): string
    {
        return __('inventories::models/scrap.title');
    }

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'                                      => __('inventories::models/scrap.log-attributes.name'),
            'origin'                                    => __('inventories::models/scrap.log-attributes.origin'),
            'state'                                     => __('inventories::models/scrap.log-attributes.state'),
            'qty'                                       => __('inventories::models/scrap.log-attributes.qty'),
            'should_replenish'                          => __('inventories::models/scrap.log-attributes.should_replenish'),
            'closed_at'                                 => __('inventories::models/scrap.log-attributes.closed_at'),
            'product.name'                              => __('inventories::models/scrap.log-attributes.product'),
            'uom.name'                                  => __('inventories::models/scrap.log-attributes.uom'),
            'lot.name'                                  => __('inventories::models/scrap.log-attributes.lot'),
            'package.name'                              => __('inventories::models/scrap.log-attributes.package'),
            'partner.name'                              => __('inventories::models/scrap.log-attributes.partner'),
            'operation.name'                            => __('inventories::models/scrap.log-attributes.operation'),
            'sourceLocation.full_name'                  => __('inventories::models/scrap.log-attributes.source-location'),
            'destinationLocation.full_name'             => __('inventories::models/scrap.log-attributes.destination-location'),
            'company.name'                              => __('inventories::models/scrap.log-attributes.company'),
            'creator.name'                              => __('inventories::models/scrap.log-attributes.creator'),
        ];
    }

    protected $casts = [
        'state'            => ScrapState::class,
        'should_replenish' => 'boolean',
        'closed_at'        => 'datetime',
        'qty'              => 'decimal:4',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
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

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'inventories_scrap_tags', 'scrap_id', 'tag_id');
    }

    public function moves(): HasMany
    {
        return $this->hasMany(Move::class);
    }

    public function moveLines(): HasManyThrough
    {
        return $this->hasManyThrough(MoveLine::class, Move::class);
    }

    public function updateName()
    {
        $this->name = 'SP/'.$this->id;
    }

    protected static function newFactory(): ScrapFactory
    {
        return ScrapFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($scrap) {
            $scrap->creator_id ??= Auth::id();
        });

        static::saving(function ($scrap) {
            $scrap->updateName();
        });
    }
}
