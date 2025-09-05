<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\OrderTemplateProductResource;

class ListOrderTemplateProducts extends ListRecords
{
    protected static string $resource = OrderTemplateProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
