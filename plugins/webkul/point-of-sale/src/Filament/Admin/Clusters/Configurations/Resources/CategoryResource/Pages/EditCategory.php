<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditCategory extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
