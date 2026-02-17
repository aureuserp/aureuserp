<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManagePayments extends BaseManagePayments
{
    use HasRecordNavigationTabs;

    protected static string $resource = InvoiceResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => PaymentResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),

                EditAction::make()
                    ->url(fn ($record) => PaymentResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ]);
    }
}
