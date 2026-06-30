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
            default               => null,
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
            default               => null,
        };
    }

    public static function shouldShowScanAction(): bool
    {
        return in_array(request()?->route()?->getName(), [
            'barcode.adjustments',
            'barcode.transfers',
            'barcode.operation',
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
