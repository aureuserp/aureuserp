<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\PaymentResource;

class ManagePayments extends ManageRelatedRecords
{
    protected static string $resource = BillResource::class;

    protected static string $relationship = 'matchedPayments';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    public static function getNavigationLabel(): string
    {
        return __('accounts::filament/resources/bill/pages/manage-payments.navigation.title');
    }

    public function table(Table $table): Table
    {
        return PaymentResource::table($table)
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
