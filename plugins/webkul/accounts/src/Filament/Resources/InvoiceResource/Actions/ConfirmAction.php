<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Account\Enums\AutoPost;
use Webkul\Account\Enums\MoveState;
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

                $record->state = MoveState::POSTED->value;
                $record->save();

                $livewire->refreshFormData(['state']);
            })
            ->hidden(function (Move $record) {
                return
                    $record->state !== MoveState::DRAFT->value ||
                    ($record->auto_post !== AutoPost::NO->value && $record->date > now());
            });
    }

    private function validateMove(Move $record): bool
    {
        if (! $record->partner_id) {
            Notification::make()
                ->warning()
                ->title(__('Customer validation'))
                ->body(__('Please provide a valid Customer to proceed with the Customer Invoice validation.'))
                ->send();

            return false;
        }

        if ($record->lines->isEmpty()) {
            Notification::make()
                ->warning()
                ->title(__('Move Line validation'))
                ->body(__('Please add at least one line to the invoice.'))
                ->send();

            return false;
        }

        return true;
    }
}
