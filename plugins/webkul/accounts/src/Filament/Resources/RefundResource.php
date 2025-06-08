<?php

namespace Webkul\Account\Filament\Resources;

use Webkul\Account\Filament\Resources\RefundResource\Pages;
use Webkul\Account\Models\Move as AccountMove;

class RefundResource extends BillResource
{
    protected static ?string $model = AccountMove::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRefunds::route('/'),
            'create' => Pages\CreateRefund::route('/create'),
            'edit'   => Pages\EditRefund::route('/{record}/edit'),
            'view'   => Pages\ViewRefund::route('/{record}'),
        ];
    }
}
