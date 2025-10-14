<?php

namespace Webkul\Account\Filament\Resources;

use Webkul\Account\Filament\Resources\RefundResource\Pages\CreateRefund;
use Webkul\Account\Filament\Resources\RefundResource\Pages\EditRefund;
use Webkul\Account\Filament\Resources\RefundResource\Pages\ListRefunds;
use Webkul\Account\Filament\Resources\RefundResource\Pages\ViewRefund;
use Webkul\Account\Models\Move as AccountMove;

class RefundResource extends BillResource
{
    protected static ?string $model = AccountMove::class;

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
}
