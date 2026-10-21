<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Throwable;
use Webkul\PointOfSale\Enums\CashMovementType;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Session;

class CashMovementAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'point-of-sale.session.cash-movement';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('point-of-sale::filament/admin/clusters/orders/actions/cash-movement.label'))
            ->icon('heroicon-o-banknotes')
            ->color('gray')
            ->schema([
                Select::make('type')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/actions/cash-movement.form.fields.type'))
                    ->options(CashMovementType::class)
                    ->native(false)
                    ->default(CashMovementType::IN)
                    ->required(),

                TextInput::make('amount')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/actions/cash-movement.form.fields.amount'))
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),

                TextInput::make('reason')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/actions/cash-movement.form.fields.reason'))
                    ->maxLength(255),
            ])
            ->action(function (Session $record, array $data): void {
                try {
                    $type = $data['type'] instanceof CashMovementType
                        ? $data['type']
                        : CashMovementType::from($data['type']);

                    $amount = (float) $data['amount'];

                    $reason = $data['reason'] ?? null;

                    $type === CashMovementType::IN
                        ? PointOfSale::cashIn($record, $amount, $reason)
                        : PointOfSale::cashOut($record, $amount, $reason);

                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/orders/actions/cash-movement.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/orders/actions/cash-movement.notification.success.body'))
                        ->send();
                } catch (Throwable $exception) {
                    Notification::make()
                        ->danger()
                        ->body($exception->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(fn (Session $record): bool => $record->isLive());
    }
}
