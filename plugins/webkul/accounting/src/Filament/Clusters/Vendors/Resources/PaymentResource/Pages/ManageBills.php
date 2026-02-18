<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Webkul\Account\Enums\MoveType;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\RefundResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageBills extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentResource::class;

    protected static string $relationship = 'invoices';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/vendors/resources/payment/pages/manage-bills.navigation.title');
    }

    public function table(Table $table): Table
    {
        return BillResource::table($table)
            ->recordActions([
                ViewAction::make()
                    ->url(function ($record) {
                        if ($record->move_type === MoveType::IN_INVOICE) {
                            return BillResource::getUrl('view', ['record' => $record]);
                        } else {
                            return RefundResource::getUrl('view', ['record' => $record]);
                        }
                    })
                    ->openUrlInNewTab(false),

                EditAction::make()
                    ->url(function ($record) {
                        if ($record->move_type === MoveType::IN_INVOICE) {
                            return BillResource::getUrl('edit', ['record' => $record]);
                        } else {
                            return RefundResource::getUrl('edit', ['record' => $record]);
                        }
                    })
                    ->openUrlInNewTab(false),
            ]);
    }
}
