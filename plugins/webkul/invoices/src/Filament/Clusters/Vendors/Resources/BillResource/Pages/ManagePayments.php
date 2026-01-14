<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\BillResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManagePayments extends BaseManagePayments
{
    use HasRecordNavigationTabs;

    protected static string $resource = BillResource::class;

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
