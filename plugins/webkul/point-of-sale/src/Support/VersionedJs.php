<?php

namespace Webkul\PointOfSale\Support;

use Filament\Support\Assets\Js;

class VersionedJs extends Js
{
    public function getVersion(): string
    {
        $path = $this->getPath();

        if ($path && is_file($path)) {
            return (string) filemtime($path);
        }

        return parent::getVersion();
    }
}
