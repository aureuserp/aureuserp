<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Throwable;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Session;

class ConfirmOpeningControlAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'point-of-sale.session.confirm-opening-control';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('point-of-sale::filament/admin/clusters/orders/actions/confirm-opening-control.label'))
            ->icon('heroicon-o-lock-open')
            ->color('primary')
            ->schema([
                TextInput::make('cash_balance_start')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/actions/confirm-opening-control.form.fields.cash-balance-start'))
                    ->numeric()
                    ->default(0)
                    ->required(),

                Textarea::make('opening_notes')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/actions/confirm-opening-control.form.fields.opening-notes'))
                    ->rows(2),
            ])
            ->action(function (Session $record, array $data): void {
                try {
                    PointOfSale::confirmSessionOpeningControl(
                        $record,
                        (float) $data['cash_balance_start'],
                        $data['opening_notes'] ?? null,
                    );

                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/orders/actions/confirm-opening-control.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/orders/actions/confirm-opening-control.notification.success.body'))
                        ->send();
                } catch (Throwable $exception) {
                    Notification::make()
                        ->danger()
                        ->body($exception->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(fn (Session $record): bool => $record->state === SessionState::OPENING_CONTROL);
    }
}
