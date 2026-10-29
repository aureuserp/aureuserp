<?php

namespace App\Filament\Support\View\Components;

use Filament\Support\Colors\Color;
use Filament\Support\View\Components\ButtonComponent as BaseButtonComponent;
use Filament\Support\View\Components\ColorMaps\ButtonComponentColorMap;

class ButtonComponent extends BaseButtonComponent
{
    public function getColorMap(array $color): array
    {
        if ($this->isOutlined) {
            return parent::getColorMap($color);
        }

        return ButtonComponentColorMap::make($color)
            ->minContrastRatio(Color::WCAG_AA_TEXT)
            ->lightBackground(bg: 600, hover: 500)
            ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
            ->get();
    }
}
