<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions\ConfirmAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions\OpenOpportunityAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\ConfirmAction as BaseConfirmAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ViewQuotation as BaseViewQuotation;

class ViewQuotation extends BaseViewQuotation
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            OpenOpportunityAction::make(),
            ...array_map(
                fn ($action) => $action instanceof BaseConfirmAction ? ConfirmAction::make() : $action,
                parent::getHeaderActions(),
            ),
        ];
    }
}
