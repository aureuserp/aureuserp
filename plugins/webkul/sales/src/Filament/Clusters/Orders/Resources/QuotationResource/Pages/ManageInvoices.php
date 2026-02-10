<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Livewire\Livewire;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageInvoices extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = QuotationResource::class;

    protected static string $relationship = 'accountMoves';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return __('Invoices');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->accountMoves()->count();
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
