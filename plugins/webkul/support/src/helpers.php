<?php

use Illuminate\Support\Number;

if (! function_exists('money')) {
    function money(float|\Closure $amount, string|\Closure|null $currency = null, int $divideBy = 0, string|\Closure|null $locale = null): string
    {
        $amount = $amount instanceof \Closure ? $amount() : $amount;

        $currency = $currency instanceof \Closure ? $currency() : ($currency ?? config('app.currency'));

        $locale = $locale instanceof \Closure ? $locale() : ($locale ?? config('app.locale'));

        if ($divideBy > 0) {
            $amount /= $divideBy;
        }

        return Number::currency($amount, $currency, $locale);
    }
}
