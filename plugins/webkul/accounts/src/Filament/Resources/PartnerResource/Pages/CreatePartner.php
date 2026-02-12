<?php

namespace Webkul\Account\Filament\Resources\PartnerResource\Pages;

use Webkul\Account\Filament\Resources\PartnerResource;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\CreatePartner as BaseCreatePartner;

class CreatePartner extends BaseCreatePartner
{
    protected static string $resource = PartnerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

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
