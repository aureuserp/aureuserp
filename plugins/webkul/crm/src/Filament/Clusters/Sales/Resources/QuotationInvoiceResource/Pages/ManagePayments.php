<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Clusters\Sales\Resources\PaymentResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages\ManagePayments as BaseManagePayments;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = QuotationInvoiceResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => PaymentResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),
                EditAction::make()
                    ->url(fn ($record): string => PaymentResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ]);
    }
}
