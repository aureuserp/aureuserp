<?php

namespace Webkul\Barcode\Support;

use Webkul\Manufacturing\Models\Order;
use Webkul\PluginManager\Package;

class Manufacturing
{
    public const PLUGIN = 'manufacturing';

    public static function isAvailable(): bool
    {
        return class_exists(Order::class) && Package::isPluginInstalled(static::PLUGIN);
    }
}
