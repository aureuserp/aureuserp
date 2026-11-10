<?php

namespace Webkul\Support\Filament\Customization;

use Filament\Navigation\NavigationItem;
use UnitEnum;

class CustomizableNavigationItem extends NavigationItem
{
    public function getGroup(): string|UnitEnum|null
    {
        $key = $this->getKey();

        if (PanelCustomizer::hasNavigationGroup($key)) {
            return PanelCustomizer::navigationGroupFor($key);
        }

        return parent::getGroup();
    }

    public function isHidden(): bool
    {
        return PanelCustomizer::isNavigationItemHidden($this->getKey()) || parent::isHidden();
    }
}
