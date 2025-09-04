<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\VendorResource;
use Webkul\Partner\Filament\Resources\Partners\Pages\ManageContacts as BaseManageContacts;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = VendorResource::class;

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        $cluster = static::getCluster();

        if (filled($cluster)) {
            return [
                $cluster::getUrl() => $cluster::getClusterBreadcrumb(),
                ...$breadcrumbs,
            ];
        }

        return $breadcrumbs;
    }
}
