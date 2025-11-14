<?php

if (!function_exists('float_check_precision')) {
    function float_check_precision($precisionDigits = null, $precisionRounding = null)
    {
        if (!is_null($precisionRounding) && is_null($precisionDigits)) {
            if ($precisionRounding <= 0) {
                throw new AssertionError("precision_rounding must be positive, got {$precisionRounding}");
            }
        } elseif (!is_null($precisionDigits) && is_null($precisionRounding)) {
            if (!is_int($precisionDigits) && (float)$precisionDigits != floor($precisionDigits)) {
                throw new AssertionError("precision_digits must be a non-negative integer, got {$precisionDigits}");
            }
            
            if ($precisionDigits < 0) {
                throw new AssertionError("precision_digits must be a non-negative integer, got {$precisionDigits}");
            }

            $precisionRounding = pow(10, -$precisionDigits);
        } else {
            throw new AssertionError("exactly one of precision_digits and precision_rounding must be specified");
        }

        return $precisionRounding;
    }
}

if (!function_exists('float_round')) {
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
