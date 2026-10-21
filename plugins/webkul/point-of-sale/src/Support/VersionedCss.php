<?php

namespace Webkul\PointOfSale\Support;

use Filament\Support\Assets\Css;

class VersionedCss extends Css
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
