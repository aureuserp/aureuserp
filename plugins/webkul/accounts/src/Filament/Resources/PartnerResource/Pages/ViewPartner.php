<?php

namespace Webkul\Account\Filament\Resources\PartnerResource\Pages;

use Webkul\Account\Filament\Resources\PartnerResource;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ViewPartner as BaseViewPartner;

class ViewPartner extends BaseViewPartner
{
    protected static string $resource = PartnerResource::class;

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
