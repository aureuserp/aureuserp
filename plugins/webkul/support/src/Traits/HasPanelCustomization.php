<?php

namespace Webkul\Support\Traits;

use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Resources\ResourceConfiguration;
use Livewire\Livewire;
use Webkul\Support\Filament\Customization\CustomizableNavigationItem;
use Webkul\Support\Filament\Customization\PanelCustomizer;
use Webkul\Support\Livewire\PageHeaderActions;

trait HasPanelCustomization
{
    protected function registerPanelCustomization(): void
    {
        Panel::macro('customizeResources', function (array $excluded, array $replaced): Panel {
            $removed = array_merge(array_keys($excluded), array_keys($replaced));

            if ($removed) {
                $this->resources = array_filter(
                    $this->resources,
                    fn ($resource): bool => ! in_array($resource instanceof ResourceConfiguration ? $resource->resource : $resource, $removed, true),
                );
            }

            if ($replaced) {
                $this->resources(array_values(array_filter(
                    array_unique($replaced),
                    fn (string $replacement): bool => ! in_array($replacement, $this->resources, true),
                )));
            }

            foreach ($this->clusteredComponents as $cluster => $components) {
                $this->clusteredComponents[$cluster] = array_values(array_unique(array_filter(
                    $components,
                    fn (string $component): bool => ! in_array($component, $removed, true),
                )));
            }

            $this->modelResources = [];

            return $this;
        });

        Panel::configureUsing(fn (Panel $panel) => PanelCustomizer::apply($panel), isImportant: true);

        $this->app->bind(NavigationItem::class, CustomizableNavigationItem::class);

        Livewire::component('page-header-actions', PageHeaderActions::class);

        $this->app->booted(fn () => PanelCustomizer::swapPageRoutes());
    }
}
