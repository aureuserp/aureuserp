<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Throwable;
use Webkul\Account\Models\Account;
use Webkul\PointOfSale\Exceptions\UnbalancedSessionException;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Session;

class CloseSessionAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'point-of-sale.session.close';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('point-of-sale::filament/admin/clusters/orders/actions/close-session.label'))
            ->icon('heroicon-o-lock-closed')
            ->color('danger')
            ->requiresConfirmation()
            ->schema(fn (Session $record): array => [
                TextInput::make('cash_balance_end_real')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/actions/close-session.form.fields.cash-balance-end-real'))
                    ->helperText(__('point-of-sale::filament/admin/clusters/orders/actions/close-session.form.fields.cash-balance-end-real-helper-text', [
                        'expected' => $record->expectedCashBalance(),
                    ]))
                    ->numeric()
                    ->default(fn (): float => $record->expectedCashBalance()),

                Textarea::make('closing_notes')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/actions/close-session.form.fields.closing-notes'))
                    ->rows(2),

                Select::make('balancing_account_id')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/actions/close-session.form.fields.balancing-account'))
                    ->helperText(__('point-of-sale::filament/admin/clusters/orders/actions/close-session.form.fields.balancing-account-helper-text'))
                    ->options(fn (): array => Account::query()
                        ->where('deprecated', false)
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->action(function (Session $record, array $data): void {
                try {
                    PointOfSale::closeSessionWithAccounting(
                        $record,
                        isset($data['cash_balance_end_real']) ? (float) $data['cash_balance_end_real'] : null,
                        $data['closing_notes'] ?? null,
                        isset($data['balancing_account_id']) ? (int) $data['balancing_account_id'] : null,
                    );

                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/orders/actions/close-session.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/orders/actions/close-session.notification.success.body'))
                        ->send();
                } catch (UnbalancedSessionException $exception) {
                    Notification::make()
                        ->warning()
                        ->title(__('point-of-sale::filament/admin/clusters/orders/actions/close-session.notification.unbalanced.title'))
                        ->body($exception->getMessage())
                        ->persistent()
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
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
