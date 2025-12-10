<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Account\Enums\AutoPost;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account as AccountFacade;
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

                $record->checked = $record->journal->auto_check_on_post;

                $record = AccountFacade::confirm($record);

                $livewire->refreshFormData(['state', 'parent_state']);
            })
            ->hidden(function (Move $record) {
                return
                    $record->state !== MoveState::DRAFT
                    || (
                        $record->auto_post !== AutoPost::NO
                        && $record->date > now()
                    );
            });
    }

    private function validateMove(Move &$record): bool
    {
        if (! $record->partner_id) {
            if ($record->isSaleDocument(true)) {
                $this->sendErrorNotification(
                    __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.customer-required.title'),
                    __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.customer-required.body')
                );

                return false;
            } elseif ($record->isPurchaseDocument(true)) {
                $this->sendErrorNotification(
                    __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.vendor-required.title'),
                    __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.vendor-required.body')
                );

                return false;
            }
        }

        if ($record->partnerBank?->trashed()) {
            $this->sendErrorNotification(
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.bank-archived.title'),
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.bank-archived.body')
            );

            return false;
        }

        if (float_compare($record->amount_total, 0, precisionRounding: $record->currency->rounding) < 0) {
            $this->sendErrorNotification(
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.negative-amount.title'),
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.negative-amount.body')
            );

            return false;
        }

        if (! $record->invoice_date) {
            if ($record->isSaleDocument(true)) {
                $record->invoice_date = now();
            } elseif ($record->isPurchaseDocument(true)) {
                $this->sendErrorNotification(
                    __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.date-required.title'),
                    __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.date-required.body')
                );

                return false;
            }
        }

        if (in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])) {
            $this->sendErrorNotification(
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.draft-state-required.title'),
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.draft-state-required.body')
            );

            return false;
        }

        if ($record->lines->isEmpty()) {
            $this->sendErrorNotification(
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.lines-required.title'),
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.lines-required.body')
            );

            return false;
        }

        if ($record->lines->some(fn ($line) => $line->account->deprecated)) {
            $this->sendErrorNotification(
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.account-deprecated.title'),
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.account-deprecated.body')
            );

            return false;
        }

        if (! $record->journal) {
            $this->sendErrorNotification(
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.journal-archived.title'),
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.journal-archived.body')
            );

            return false;
        }

        if (! $record->currency) {
            $this->sendErrorNotification(
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.currency-archived.title'),
                __('accounts::filament/resources/invoice/actions/confirm-action.validation.notification.error.currency-archived.body')
            );

            return false;
        }

        return true;
    }

    private function sendErrorNotification(string $title, string $body): void
    {
        Notification::make()
            ->warning()
            ->title($title)
            ->body($body)
            ->send();
    }
}
