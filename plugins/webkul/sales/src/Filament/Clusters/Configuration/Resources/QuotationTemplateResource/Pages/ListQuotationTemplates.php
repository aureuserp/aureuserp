<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplateResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplateResource;

class ListQuotationTemplates extends ListRecords
{
    protected static string $resource = QuotationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
