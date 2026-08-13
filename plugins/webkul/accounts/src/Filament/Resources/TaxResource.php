<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Exception;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\TaxResource\Pages\CreateTax;
use Webkul\Account\Filament\Resources\TaxResource\Pages\EditTax;
use Webkul\Account\Filament\Resources\TaxResource\Pages\ListTaxes;
use Webkul\Account\Filament\Resources\TaxResource\Pages\ViewTax;
use Webkul\Account\Filament\Resources\TaxResource\Schemas\TaxForm;
use Webkul\Account\Filament\Resources\TaxResource\Schemas\TaxInfolist;
use Webkul\Account\Filament\Resources\TaxResource\Tables\TaxesTable;
use Webkul\Account\Models\Tax;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function form(Schema $schema): Schema
    {
        return TaxForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaxInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaxes::route('/'),
            'create' => CreateTax::route('/create'),
            'view'   => ViewTax::route('/{record}'),
            'edit'   => EditTax::route('/{record}/edit'),
        ];
    }

    public static function validateRepartitionData(array $invoiceLines, array $refundLines): void
    {
        $invoice = collect($invoiceLines)->values();
        $refund = collect($refundLines)->values();

        if (
            $invoice->where('repartition_type', 'base')->count() !== 1 ||
            $refund->where('repartition_type', 'base')->count() !== 1
        ) {
            throw new Exception('Each must contain exactly one BASE repartition line.');
        }

        if (
            $invoice->where('repartition_type', 'tax')->isEmpty() ||
            $refund->where('repartition_type', 'tax')->isEmpty()
        ) {
            throw new Exception('Each must contain at least one TAX repartition line.');
        }

        if ($invoice->count() !== $refund->count()) {
            throw new Exception('Invoice and refund must have the same number of repartition lines.');
        }

        foreach ($invoice as $index => $invLine) {

            $refLine = $refund[$index] ?? null;

            $invPercent = (float) ($invLine['factor_percent'] ?? 0);
            $refPercent = (float) ($refLine['factor_percent'] ?? 0);

            if (
                ! $refLine ||
                $invLine['repartition_type'] !== $refLine['repartition_type'] ||
                $invPercent !== $refPercent

            ) {
                throw new Exception('Line #'.($index + 1).' does not match between Invoice and Refund.');
            }
        }

        $positive = $invoice
            ->filter(fn ($l) => $l['repartition_type'] === 'tax' && is_numeric($l['factor_percent'] ?? null) && $l['factor_percent'] > 0)
            ->sum(fn ($l) => (float) $l['factor_percent']);

        $negative = $invoice
            ->filter(fn ($l) => $l['repartition_type'] === 'tax' && is_numeric($l['factor_percent'] ?? null) && $l['factor_percent'] < 0)
            ->sum(fn ($l) => (float) $l['factor_percent']);

        if (bccomp(number_format($positive, 2, '.', ''), '100', 2) !== 0) {
            throw new Exception("Invoice total positive TAX percentages must equal 100% (got {$positive}%).");
        }

        if ($negative && bccomp(number_format($negative, 2, '.', ''), '-100', 2) !== 0) {
            throw new Exception("Invoice total negative TAX percentages must equal -100% (got {$negative}%).");
        }
    }
}
