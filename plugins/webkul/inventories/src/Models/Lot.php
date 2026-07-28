<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\LotFactory;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class Lot extends Model
{
    use HasFactory;

    protected $table = 'inventories_lots';

    protected $fillable = [
        'name',
        'description',
        'reference',
        'properties',
        'expiry_reminded',
        'expiration_date',
        'use_date',
        'removal_date',
        'alert_date',
        'product_id',
        'uom_id',
        'location_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'properties'      => 'array',
        'expiry_reminded' => 'boolean',
        'expiration_date' => 'datetime',
        'use_date'        => 'datetime',
        'removal_date'    => 'datetime',
        'alert_date'      => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quantities(): HasMany
    {
        return $this->hasMany(ProductQuantity::class);
    }

    public function getTotalQuantityAttribute()
    {
        return $this->quantities()
            ->whereHas('location', function ($query) {
                $query->where('type', LocationType::INTERNAL)
                    ->where('is_scrap', false);
            })
            ->sum('quantity');
    }

    public function generateLotNames(string $firstLot, int $count): array
    {
        preg_match_all('/\d+/', $firstLot, $matches);

        $caughtInitialNumber = $matches[0];

        if (empty($caughtInitialNumber)) {
            return $this->generateLotNames($firstLot.'0', $count);
        }

        $initialNumber = last($caughtInitialNumber);

        $padding = strlen($initialNumber);

        $splitted = preg_split('/'.preg_quote($initialNumber, '/').'/', $firstLot);

        $prefix = implode($initialNumber, array_slice($splitted, 0, -1));

        $suffix = last($splitted);

        $initialNumber = (int) $initialNumber;

        return array_map(fn ($i) => [
            'lot_name' => sprintf('%s%s%s', $prefix, str_pad($initialNumber + $i, $padding, '0', STR_PAD_LEFT), $suffix),
        ], range(0, $count - 1));
    }

    public static function getNextSerial(Company $company, Product $product): string
    {
        $lastSerial = static::where(function ($q) use ($company) {
            $q->where('company_id', $company->id)
                ->orWhereNull('company_id');
        })
            ->where('product_id', $product->id)
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastSerial) {
            return (new static)->generateLotNames($lastSerial->name, 2)[1]['lot_name'];
        }

        return '0001';
    }

    protected static function newFactory(): LotFactory
    {
        return LotFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lot) {
            $lot->creator_id ??= Auth::id();
        });
    }
}
