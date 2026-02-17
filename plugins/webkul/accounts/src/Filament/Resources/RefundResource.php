<?php

namespace Webkul\Account\Filament\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\RefundResource\Pages\CreateRefund;
use Webkul\Account\Filament\Resources\RefundResource\Pages\EditRefund;
use Webkul\Account\Filament\Resources\RefundResource\Pages\ListRefunds;
use Webkul\Account\Filament\Resources\RefundResource\Pages\ViewRefund;
use Webkul\Account\Models\Refund;

class RefundResource extends BillResource
{
    protected static ?string $model = Refund::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    public static function getPages(): array
    {
        return [
            'index'  => ListRefunds::route('/'),
            'create' => CreateRefund::route('/create'),
            'edit'   => EditRefund::route('/{record}/edit'),
            'view'   => ViewRefund::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(Str::contains(static::class, 'RefundResource'), function (Builder $query) {
                $query->where('move_type', MoveType::IN_REFUND);
            })
            ->orderByDesc('id');
    }
}
