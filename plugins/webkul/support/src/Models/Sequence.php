<?php

namespace Webkul\Support\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Webkul\Support\Enums\SequenceResetFrequency;
use Webkul\Support\Traits\BelongsToCompany;

class Sequence extends Model
{
    use BelongsToCompany;

    protected $table = 'sequences';

    protected $fillable = [
        'code',
        'scope_type',
        'scope_id',
        'variant',
        'name',
        'prefix',
        'suffix',
        'padding',
        'next_number',
        'step',
        'reset_frequency',
        'period_key',
        'company_id',
    ];

    protected $casts = [
        'padding'         => 'integer',
        'next_number'     => 'integer',
        'step'            => 'integer',
        'reset_frequency' => SequenceResetFrequency::class,
    ];

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($sequence) {
            if ($sequence->exists && $sequence->isDirty('reset_frequency')) {
                $sequence->period_key = null;
            }
        });
    }

    public function scope(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'scope_type', 'scope_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function consumeNumber(?CarbonInterface $date = null): string
    {
        $date ??= now();

        $periodKey = $this->periodKeyFor($date);

        if ($periodKey === null || $this->period_key === null) {
            $this->period_key = $periodKey;
        } elseif ($periodKey !== $this->period_key) {
            $this->next_number = 1;
            $this->period_key = $periodKey;
        }

        $number = max(1, $this->next_number);

        $this->next_number = $number + max(1, $this->step);

        $this->save();

        return $this->formatNumber($number, $date);
    }

    public function formatNumber(int $number, ?CarbonInterface $date = null): string
    {
        $date ??= now();

        return $this->interpolate($this->prefix, $date)
            .str_pad((string) $number, max(1, $this->padding), '0', STR_PAD_LEFT)
            .$this->interpolate($this->suffix, $date);
    }

    protected function periodKeyFor(CarbonInterface $date): ?string
    {
        return match ($this->reset_frequency) {
            SequenceResetFrequency::YEARLY  => $date->format('Y'),
            SequenceResetFrequency::MONTHLY => $date->format('Y-m'),
            default                         => null,
        };
    }

    protected function interpolate(?string $template, CarbonInterface $date): string
    {
        if (blank($template)) {
            return '';
        }

        return strtr($template, [
            '%(year)'  => $date->format('Y'),
            '%(y)'     => $date->format('y'),
            '%(month)' => $date->format('m'),
            '%(day)'   => $date->format('d'),
        ]);
    }
}
