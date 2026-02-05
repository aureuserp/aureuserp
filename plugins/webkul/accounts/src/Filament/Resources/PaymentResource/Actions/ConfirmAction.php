<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Actions;

use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Payment;

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
            ->color('primary')
            ->requiresConfirmation()
            ->action(function (Payment $record, Component $livewire): void {
                $record = AccountFacade::postPayment($record);

                if (! $record->move_id && in_array($record->state, [PaymentStatus::IN_PROCESS, PaymentStatus::PAID])) {
                    $record->generateJournalEntry();

                    $record->refresh();
                }

                AccountFacade::confirmMove($record->move);

                $livewire->refreshFormData(['state']);
            })
            ->hidden(fn (Payment $record) => $record->state != PaymentStatus::DRAFT);
    }
}
