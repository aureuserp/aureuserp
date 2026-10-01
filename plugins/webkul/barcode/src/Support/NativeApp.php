<?php

namespace Webkul\Barcode\Support;

use Webkul\NativephpRemote\Support\NativeRemote;

class NativeApp extends NativeRemote
{
    public static function startUrl(): string
    {
        return '/admin/barcode?nativephp=1';
    }

    public static function headerTitle(): ?string
    {
        $routeName = request()?->route()?->getName();

        return match ($routeName) {
            'barcode.dashboard'   => __('barcode::app.title'),
            'barcode.adjustments' => __('barcode::app.adjustments.title'),
            'barcode.transfers'   => request()?->route('operationType')?->name,
            'barcode.operation'   => request()?->route('operation')?->name,

            'barcode.manufacturing-orders' => __('barcode::app.manufacturing.title'),
            'barcode.manufacturing-order'  => request()?->route('order')?->name,
            default                        => null,
        };
    }

    public static function headerSubtitle(): ?string
    {
        $routeName = request()?->route()?->getName();

        return match ($routeName) {
            'barcode.dashboard'   => __('barcode::app.dashboard.operations'),
            'barcode.adjustments' => __('barcode::app.adjustments.subtitle'),
            'barcode.transfers'   => __('barcode::app.dashboard.operations'),
            'barcode.operation'   => request()?->route('operationType')?->name,

            'barcode.manufacturing-orders' => __('barcode::app.manufacturing.subtitle'),
            'barcode.manufacturing-order'  => request()?->route('order')?->product?->name,
            default                        => null,
        };
    }

    public static function shouldShowScanAction(): bool
    {
        return in_array(request()?->route()?->getName(), [
            'barcode.adjustments',
            'barcode.transfers',
            'barcode.operation',
            'barcode.manufacturing-orders',
            'barcode.manufacturing-order',
        ], true);
    }

    public static function scanActionUrl(): ?string
    {
        if (! static::shouldShowScanAction()) {
            return null;
        }

        return static::hashActionUrl('scan-barcode');
    }
}
