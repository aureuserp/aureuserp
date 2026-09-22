<?php

namespace Webkul\Barcode\Support;

class Barcode
{
    public static function normalize(?string $barcode): string
    {
        $barcode = trim((string) $barcode);
        $barcode = preg_replace('/\s+/', ' ', $barcode) ?: '';
        $barcode = preg_replace('/^packing\s+slip\s*/i', '', $barcode) ?: $barcode;

        return trim($barcode, " \t\n\r\0\x0B#");
    }

    public static function matches(?string $value, ?string $barcode): bool
    {
        if ($value === null || $barcode === null) {
            return false;
        }

        return mb_strtolower(static::normalize($value)) === mb_strtolower(static::normalize($barcode));
    }
}
