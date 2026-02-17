<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Webkul\Account\Enums\MoveType;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CreditNoteResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageInvoices extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentResource::class;

    protected static string $relationship = 'invoices';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/payment/pages/manage-invoices.navigation.title');
    }

    public function table(Table $table): Table
    {
        return InvoiceResource::table($table)
            ->recordActions([
                ViewAction::make()
                    ->url(function ($record) {
                        if ($record->move_type === MoveType::OUT_INVOICE) {
                            return InvoiceResource::getUrl('view', ['record' => $record]);
                        } else {
                            return CreditNoteResource::getUrl('view', ['record' => $record]);
                        }
                    })
                    ->openUrlInNewTab(false),

                EditAction::make()
                    ->url(function ($record) {
                        if ($record->move_type === MoveType::OUT_INVOICE) {
                            return InvoiceResource::getUrl('edit', ['record' => $record]);
                        } else {
                            return CreditNoteResource::getUrl('edit', ['record' => $record]);
                        }
                    })
                    ->openUrlInNewTab(false),
            ])
            ->toolbarActions([]);
    }
}
