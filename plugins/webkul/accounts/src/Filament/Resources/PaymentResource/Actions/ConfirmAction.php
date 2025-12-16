<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Actions;

use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Models\Payment;
use Webkul\Account\Facades\Account as AccountFacade;

class ConfirmAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'customers.payment.confirm';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/payment/actions/confirm-action.title'))
            ->color('gray')
            ->requiresConfirmation()
            ->action(function (Payment $record, Component $livewire): void {
                $record = AccountFacade::postPayment($record);

                if (! $record->move_id && in_array($record->state, [PaymentStatus::IN_PROCESS, PaymentStatus::PAID])) {
                    $record->generateJournalEntry();

                    $record->refresh();

                    AccountFacade::confirmMove($record->move);
                }
                
                AccountFacade::confirmMove($record->move);

                $livewire->refreshFormData(['state']);
            })
            ->hidden(function (Payment $record) {
                return $record->state != PaymentStatus::DRAFT;
            });
    }
}
