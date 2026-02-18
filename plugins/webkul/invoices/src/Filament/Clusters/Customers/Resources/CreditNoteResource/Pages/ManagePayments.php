<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\CreditNoteResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CreditNoteResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManagePayments extends BaseManagePayments
{
    use HasRecordNavigationTabs;

    protected static string $resource = CreditNoteResource::class;

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
            ])
            ->toolbarActions([]);
    }
}
