<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Account\Enums\AutoPost;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account;
use Webkul\Account\Models\Move;

class ConfirmAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.confirm';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/confirm-action.title'))
            ->color('gray')
            ->action(function (Move $record, Component $livewire): void {
                if (! $this->validateMove($record)) {
                    return;
                }

                $record = Account::confirm($record);

                $livewire->refreshFormData(['state', 'parent_state']);
            })
            ->hidden(function (Move $record) {
                return
                    $record->state !== MoveState::DRAFT ||
                    ($record->auto_post !== AutoPost::NO && $record->date > now());
            });
    }

    private function validateMove(Move $record): bool
    {
        if (! $record->partner_id) {
            Notification::make()
                ->warning()
                ->title(__('accounts::filament/resources/invoice/actions/confirm-action.customer.notification.customer-validation.title'))
                ->body(__('accounts::filament/resources/invoice/actions/confirm-action.customer.notification.customer-validation.body'))
                ->send();

            return false;
        }

        if ($record->lines->isEmpty()) {
            Notification::make()
                ->warning()
                ->title(__('Move Line validation'))
                ->body(__('Please add at least one line to the invoice.'))

                ->title(__('accounts::filament/resources/invoice/actions/confirm-action.customer.notification.move-line-validation.title'))
                ->body(__('accounts::filament/resources/invoice/actions/confirm-action.customer.notification.move-line-validation.body'))
                ->send();

            return false;
        }

        return true;
    }
}
