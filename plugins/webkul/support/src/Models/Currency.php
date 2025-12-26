<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'iso_numeric',
        'decimal_places',
        'full_name',
        'rounding',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get all states for the country.
     *
     * @return HasMany
     */
    public function rates()
    {
        return $this->hasMany(CurrencyRate::class);
    }

    public function convert(float|int $fromAmount, Currency $toCurrency, ?Company $company = null, $date = null, bool $round = true): float
    {
        $base = $this ?? $toCurrency;

        $toCurrency = $toCurrency ?? $this;

        if ($fromAmount) {
            $rate = $this->getConversionRate($base, $toCurrency, $company, $date);

            $toAmount = $fromAmount * $rate;
        } else {
            return 0.0;
        }

        return $round ? $toCurrency->round($toAmount) : $toAmount;
    }

    public function getConversionRate($fromCurrency, $toCurrency, $company = null, $date = null)
    {
        if ($fromCurrency->id === $toCurrency->id) {
            return 1;
        }

        $company = $company ?? auth()->user()->defaultCompany ?? null;

        $date = $date ?? now()->toDateString();

        $toRateRecord = $toCurrency->rates()
            //->where('company_id', $company?->id)
            ->whereDate('name', '<=', $date)
            ->orderByDesc('name')
            ->first();

        return $toRateRecord->rate ?? 1.0;
    }

    /**
     * //TODO: Implement proper rounding logic.
     */
    public function round(float $amount): float
    {
        return $amount;
    }

    public function compareAmounts($amount1, $amount2)
    {
        return float_compare($amount1, $amount2, precisionRounding: $this->rounding);
    }

    public function isZero($amount)
    {
        return $this->floatIsZero($amount, precisionRounding: $this->rounding);
    }

    protected function floatIsZero($value, $precisionDigits = null, $precisionRounding = null)
    {
        $epsilon = $this->floatCheckPrecision($precisionDigits, $precisionRounding);

        return $value == 0.0 || abs($this->floatRound($value, $epsilon)) < $epsilon;
    }

    protected function floatCheckPrecision($precisionDigits = null, $precisionRounding = null)
    {
        if ($precisionRounding !== null && $precisionDigits === null) {
            if ($precisionRounding <= 0) {
                throw new \InvalidArgumentException("precision_rounding must be positive, got {$precisionRounding}");
            }

            return $precisionRounding;
        } elseif ($precisionDigits !== null && $precisionRounding === null) {
            if (! is_int($precisionDigits) && ! $this->isInteger($precisionDigits)) {
                throw new \InvalidArgumentException("precision_digits must be a non-negative integer, got {$precisionDigits}");
            }

            if ($precisionDigits < 0) {
                throw new \InvalidArgumentException("precision_digits must be a non-negative integer, got {$precisionDigits}");
            }

            return pow(10, -$precisionDigits);
        } else {
            throw new \InvalidArgumentException('exactly one of precision_digits and precision_rounding must be specified');
        }
    }

    protected function floatRound($value, $precisionRounding)
    {
        if ($precisionRounding == 0) {
            return $value;
        }

        return round($value / $precisionRounding) * $precisionRounding;
    }

    protected function isInteger($value)
    {
        return is_numeric($value) && floatval($value) == intval($value);
    }
}
