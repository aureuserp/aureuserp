<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\CreateCreditNote;
use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\EditCreditNote;
use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\ListCreditNotes;
use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\ViewCreditNote;
use Webkul\Account\Models\CreditNote;

class CreditNoteResource extends InvoiceResource
{
    protected static ?string $model = CreditNote::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    public static function getPages(): array
    {
        return [
            'index'  => ListCreditNotes::route('/'),
            'create' => CreateCreditNote::route('/create'),
            'edit'   => EditCreditNote::route('/{record}/edit'),
            'view'   => ViewCreditNote::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(Str::contains(static::class, 'CreditNoteResource'), function (Builder $query) {
                $query->where('move_type', MoveType::OUT_REFUND);
            })
            ->orderByDesc('id');
    }
}
