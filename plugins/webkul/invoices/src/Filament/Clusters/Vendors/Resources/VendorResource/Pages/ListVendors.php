<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Filament\Actions\CreateAction;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ListPartners as BaseListVendors;

class ListVendors extends BaseListVendors
{
    protected static string $resource = VendorResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('invoices::filament/clusters/vendors/resources/vendor/pages/list-vendor.title');
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

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('invoices::filament/clusters/vendors/resources/vendor/pages/list-vendor.header-actions.create.title'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
