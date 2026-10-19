<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Pages\Enums\SubNavigationPosition;

/**
 * Keeps the CRM cluster menu (e.g. My Pipeline, Teams, Contacts) in the left sidebar
 * on pages that would otherwise show no sub-navigation, such as create, view and edit
 * pages of resources reused from other plugins.
 *
 * A cluster with a single menu item (e.g. Leads) shows no left sidebar at all.
 */
trait HasCrmClusterNavigation
{
    public function getSubNavigation(): array
    {
        if (blank($cluster = static::getCluster())) {
            return [];
        }

        $navigation = $this->generateNavigationItems($cluster::getClusteredComponents());

        return count($navigation) > 1 ? $navigation : [];
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }
}
