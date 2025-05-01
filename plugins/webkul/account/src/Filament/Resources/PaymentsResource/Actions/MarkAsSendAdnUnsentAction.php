<?php

namespace Webkul\Account\Filament\Resources\PaymentsResource\Actions;

use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Models\Payment;

class MarkAsSendAdnUnsentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'customers.payment.mark-as-sent-or-unsent';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(fn (Payment $record) => $record->is_sent ? __('accounts::filament/resources/payment/actions/set-as-send-and-unsend-action.unmark-as-sent') : __('accounts::filament/resources/payment/actions/set-as-send-and-unsend-action.mark-as-sent'))
            ->color('gray')
            ->action(function (Payment $record, Component $livewire): void {
                $record->is_sent = ! $record->is_sent;
                $record->save();

                $livewire->refreshFormData(['state']);
            })
            ->hidden(function (Payment $record) {
                return $record->state !== PaymentStatus::IN_PROCESS->value
                    || ($record->paymentMethodLine?->paymentMethod?->code ?? '') !== 'manual';
            });
    }
}
