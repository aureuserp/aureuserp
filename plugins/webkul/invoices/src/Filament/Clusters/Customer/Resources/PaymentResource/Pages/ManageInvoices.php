<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentResource;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\InvoiceResource;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNoteResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\Account\Enums\MoveType;

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
