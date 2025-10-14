<?php

namespace Webkul\Support\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\UOMFactory;

class UOM extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'unit_of_measures';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'factor',
        'category_id',
        'creator_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(UOMCategory::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convert the given quantity from the current UoM to a given one
     *
     * @param  float  $qty  The quantity to convert
     * @param  UOM  $toUnit  The destination UoM record
     * @param  bool  $round  Whether to round the result
     * @param  string  $roundingMethod  The rounding method ('UP', 'DOWN', etc.)
     * @param  bool  $raiseIfFailure  Whether to throw an exception on conversion failure
     * @return float The converted quantity
     *
     * @throws Exception If conversion fails and $raiseIfFailure is true
     */
    public function computeQuantity($qty, $toUnit, $round = true, $roundingMethod = 'UP', $raiseIfFailure = true)
    {
        if (! $this || ! $qty) {
            return $qty;
        }

        if ($this->id !== $toUnit->id && $this->category_id !== $toUnit->category_id) {
            if ($raiseIfFailure) {
                throw new Exception(__(
                    'The unit of measure :unit defined on the order line doesn\'t belong to the same category as the unit of measure :product_unit defined on the product. Please correct the unit of measure defined on the order line or on the product. They should belong to the same category.',
                    ['unit' => $this->name, 'product_unit' => $toUnit->name]
                ));
            } else {
                return $qty;
            }
        }

        if ($this->id === $toUnit->id) {
            $amount = $qty;
        } else {
            $amount = $qty / $this->factor;

            if ($toUnit) {
                $amount = $amount * $toUnit->factor;
            }
        }

        if ($toUnit && $round) {
            $amount = $this->floatRound(
                $amount,
                $toUnit->rounding,
                $roundingMethod
            );
        }

        return $amount;
    }

    /**
     * Custom float rounding implementation
     *
     * @param  float  $value  The value to round
     * @param  float  $precision  The precision to round to
     * @param  string  $method  The rounding method
     * @return float The rounded value
     */
    private function floatRound($value, $precision, $method = 'UP')
    {
        if ($precision == 0) {
            return $value;
        }

        $factor = 1.0 / $precision;

        switch (strtoupper($method)) {
            case 'CEILING':
            case 'UP':
                return ceil($value * $factor) / $factor;

            case 'FLOOR':
            case 'DOWN':
                return floor($value * $factor) / $factor;

            case 'HALF-UP':
                return round($value * $factor) / $factor;

            default:
                return round($value * $factor) / $factor;
        }
    }

    protected static function newFactory(): UOMFactory
    {
        return UOMFactory::new();
    }
}
