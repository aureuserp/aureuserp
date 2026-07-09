<?php

use Illuminate\Support\Number;
use Webkul\Support\SettingsRegistry;

if (! function_exists('settings')) {
    function settings(string $settings): object
    {
        return app(SettingsRegistry::class)->get($settings);
    }
}

if (! function_exists('money')) {
    function money(float|Closure $amount, string|Closure|null $currency = null, int $divideBy = 0, string|Closure|null $locale = null): string
    {
        $amount = $amount instanceof Closure ? $amount() : $amount;

        $currency = $currency instanceof Closure ? $currency() : ($currency ?? config('app.currency'));

        $locale = $locale instanceof Closure ? $locale() : ($locale ?? config('app.locale'));

        if ($divideBy > 0) {
            $amount /= $divideBy;
        }

        return Number::currency($amount, $currency, $locale);
    }

    if (! function_exists('random_color')) {
        function random_color(string $type = 'hex'): string
        {
            return match (strtolower($type)) {
                'rgb' => sprintf(
                    'rgb(%d, %d, %d)',
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 255)
                ),

                'rgba' => sprintf(
                    'rgba(%d, %d, %d, %.2f)',
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 100) / 100
                ),

                'hsl' => sprintf(
                    'hsl(%d, %d%%, %d%%)',
                    random_int(0, 360),
                    random_int(30, 100),
                    random_int(20, 80)
                ),

                'hex' => sprintf(
                    '#%02X%02X%02X',
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 255)
                ),

                default => throw new InvalidArgumentException(
                    'Invalid color type. Use: hex, rgb, rgba, or hsl'
                ),
            };
        }
    }
}

if (! function_exists('format_float_time')) {
    function format_float_time(mixed $state, string $unit = 'minutes'): string
    {
        $value = (float) ($state ?? 0);
        $primary = (int) floor($value);
        $secondary = (int) round(($value - $primary) * 60);

        if ($secondary === 60) {
            $primary++;
            $secondary = 0;
        }

        return sprintf('%02d:%02d', $primary, $secondary);
    }
}

if (! function_exists('parse_float_time')) {
    function parse_float_time(?string $state, string $unit = 'minutes'): string
    {
        if (! is_string($state) || ! preg_match('/^(?<primary>\d+):(?<secondary>\d{2})$/', $state, $matches)) {
            return '60';
        }

        $secondary = (int) $matches['secondary'];

        if ($secondary > 59) {
            return '60';
        }

        $primary = (int) $matches['primary'];

        return (string) ($primary + ($secondary / 60));
    }
}

if (! function_exists('float_is_zero')) {
    function float_is_zero($value, $precisionDigits = null, $precisionRounding = null)
    {
        $epsilon = float_check_precision($precisionDigits, $precisionRounding);

        if ($value == 0.0) {
            return true;
        }

        return abs(float_round($value, precisionRounding: $epsilon)) < $epsilon;
    }
}

if (! function_exists('float_compare')) {
    function float_compare($value1, $value2, $precisionDigits = null, $precisionRounding = null)
    {
        $roundingFactor = float_check_precision($precisionDigits, $precisionRounding);

        if ($value1 == $value2) {
            return 0;
        }

        $value1 = float_round($value1, precisionRounding: $roundingFactor);
        $value2 = float_round($value2, precisionRounding: $roundingFactor);

        $delta = $value1 - $value2;

        if (float_is_zero($delta, null, precisionRounding: $roundingFactor)) {
            return 0;
        }

        return $delta < 0.0 ? -1 : 1;
    }
}

if (! function_exists('float_check_precision')) {
    function float_check_precision($precisionDigits = null, $precisionRounding = null)
    {
        if (! is_null($precisionRounding) && is_null($precisionDigits)) {
            if ($precisionRounding <= 0) {
                throw new AssertionError("precision_rounding must be positive, got {$precisionRounding}");
            }
        } elseif (! is_null($precisionDigits) && is_null($precisionRounding)) {
            if (! is_int($precisionDigits) && (float) $precisionDigits != floor($precisionDigits)) {
                throw new AssertionError("precision_digits must be a non-negative integer, got {$precisionDigits}");
            }

            if ($precisionDigits < 0) {
                throw new AssertionError("precision_digits must be a non-negative integer, got {$precisionDigits}");
            }

            $precisionRounding = pow(10, -$precisionDigits);
        } else {
            throw new AssertionError('exactly one of precision_digits and precision_rounding must be specified');
        }

        return $precisionRounding;
    }
}

if (! function_exists('float_round')) {
    function float_round($value, $precisionDigits = null, $precisionRounding = null, $roundingMethod = 'HALF-UP')
    {
        $roundingFactor = float_check_precision($precisionDigits, $precisionRounding);

        if ($roundingFactor == 0 || $value == 0) {
            return 0.0;
        }

        $scaled = $value / $roundingFactor;
        $roundingMethod = strtoupper($roundingMethod);

        switch ($roundingMethod) {
            case 'HALF-UP':
                $rounded = ($scaled > 0)
                    ? floor($scaled + 0.5)
                    : ceil($scaled - 0.5);
                break;

            case 'HALF-DOWN':
                $rounded = ($scaled > 0)
                    ? ceil($scaled - 0.5)
                    : floor($scaled + 0.5);
                break;

            case 'HALF-EVEN':
                $floor = floor($scaled);
                $diff = abs($scaled - $floor);
                if ($diff == 0.5) {
                    $rounded = ($floor % 2 == 0)
                        ? $floor
                        : $floor + ($scaled > 0 ? 1 : -1);
                } else {
                    $rounded = round($scaled);
                }
                break;

            case 'UP':
                $rounded = ($scaled > 0)
                    ? ceil($scaled)
                    : floor($scaled);
                break;

            case 'DOWN':
                $rounded = ($scaled > 0)
                    ? floor($scaled)
                    : ceil($scaled);
                break;

            default:
                throw new InvalidArgumentException("Unknown rounding method: {$roundingMethod}");
        }

        return $rounded * $roundingFactor;
    }
}

if (! function_exists('make_aware')) {
    /**
     * Return $dt with an explicit timezone, together with a callable to
     * convert a Carbon back to the same timezone as $dt.
     *
     * Mirrors Python's make_aware(dt):
     *   if dt.tzinfo → return dt, lambda val: val.astimezone(dt.tzinfo)
     *   else          → return dt.replace(tzinfo=utc), lambda val: val.astimezone(utc)
     *
     * Carbon always carries a timezone, so the first branch always applies.
     * The second branch (naive datetime → UTC) is handled by ensuring the
     * caller passes a UTC Carbon when no explicit timezone is intended.
     *
     * @return array{0: Carbon\Carbon, 1: callable(Carbon\Carbon): Carbon\Carbon}
     */
    function make_aware(Carbon\Carbon $dt): array
    {
        $tz = $dt->getTimezone();

        return [$dt, fn (Carbon\Carbon $val) => $val->clone()->setTimezone($tz)];
    }
}
