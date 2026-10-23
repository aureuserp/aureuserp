<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Webkul\Crm\Filament\Actions\CrmExportAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Exports\QuotationExporter;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations as BaseListQuotations;

class ListQuotations extends BaseListQuotations
{
    protected static string $resource = QuotationResource::class;

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/quotation.pages.list.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CrmExportAction::make()
                ->exporter(QuotationExporter::class),
            ...parent::getHeaderActions(),
        ];
    }
}
