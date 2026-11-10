<?php

namespace Webkul\Support\Filament\Customization;

use Filament\Panel;
use Filament\PanelRegistry;
use LogicException;

class CustomizablePanelRegistry extends PanelRegistry
{
    public static function from(PanelRegistry $registry): static
    {
        if ($registry->panels) {
            throw new LogicException('Panels ['.implode(', ', array_keys($registry->panels)).'] were registered before panel customization was set up, so their customizations would not apply. Do not resolve the Filament panel registry while service providers register.');
        }

        $customizable = new static;

        $customizable->defaultPanel = $registry->defaultPanel;

        return $customizable;
    }

    public function register(Panel $panel): void
    {
        PanelCustomizer::apply($panel);

        parent::register($panel);
    }
}
