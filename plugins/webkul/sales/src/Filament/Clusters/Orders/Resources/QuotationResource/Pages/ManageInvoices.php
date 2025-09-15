<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;

class ManageInvoices extends ManageRelatedRecords
{
    protected static string $resource = QuotationResource::class;

    protected static string $relationship = 'accountMoves';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    public static function getNavigationLabel(): string
    {
        return __('Invoices');
    }

    public function table(Table $table): Table
    {
        return InvoiceResource::table($table)
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => InvoiceResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),

                EditAction::make()
                    ->url(fn ($record) => InvoiceResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ]);
    }
}
