<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions\ConfirmAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\ConfirmAction as BaseConfirmAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\EditQuotation as BaseEditQuotation;

class EditQuotation extends BaseEditQuotation
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...array_map(
                fn ($action) => $action instanceof BaseConfirmAction ? ConfirmAction::make() : $action,
                parent::getHeaderActions(),
            ),
        ];
    }
}
