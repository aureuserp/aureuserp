<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Actions;

use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Payment;

class ResetToDraftAction extends Action
{
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
                $record->state = PaymentStatus::DRAFT;
                $record->save();

                if ($record->move) {
                    $record->move = AccountFacade::resetToDraftMove($record->move);
                }

                $livewire->refreshFormData(['state']);
            })
            ->hidden(fn (Payment $record) => $record->state == PaymentStatus::DRAFT);
    }
}
