<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Payment;

class ResetToDraftAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'customers.payment.reset-to-draft';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/payment/actions/reset-to-draft.title'))
            ->color('gray')
            ->action(function (Payment $record, Component $livewire): void {
                try {
                    AccountFacade::resetToDraftPayment($record);

                    $livewire->refreshFormData(['state']);
                } catch (Throwable $e) {
                    Notification::make()
                        ->warning()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->hidden(fn (Payment $record) => $record->state == PaymentStatus::DRAFT);
    }
}
