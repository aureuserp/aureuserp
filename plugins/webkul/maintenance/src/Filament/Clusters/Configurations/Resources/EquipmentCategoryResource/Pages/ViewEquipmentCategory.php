<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource;

class ViewEquipmentCategory extends ViewRecord
{
    protected static string $resource = EquipmentCategoryResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
