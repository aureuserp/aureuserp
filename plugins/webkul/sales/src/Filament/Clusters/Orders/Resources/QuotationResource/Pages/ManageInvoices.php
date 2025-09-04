<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;

class ManageInvoices extends ManageRelatedRecords
{
    protected static string $resource = QuotationResource::class;

    protected static string $relationship = 'accountMoves';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public function getSubNavigationPosition(): SubNavigationPosition
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
            ->query($this->getTableQuery())
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => InvoiceResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),

                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => InvoiceResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return InvoiceResource::getEloquentQuery();
    }
}
