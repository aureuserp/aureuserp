<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class CreateLot extends CreateRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = LotResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
