<?php

use Illuminate\Support\Number;

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
