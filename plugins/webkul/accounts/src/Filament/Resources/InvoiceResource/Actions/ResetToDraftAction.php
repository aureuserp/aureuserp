<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;

class ResetToDraftAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.reset-to-draft';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/reset-to-draft-action.title'))
            ->color('gray')
            ->icon('heroicon-o-arrow-path')
            ->action(function (Move $record, Component $livewire): void {
                if (! $this->validateMove($record)) {
                    return;
                }

                $record = AccountFacade::resetToDraftMove($record);

                $record->save();

                $livewire->refreshFormData(['state', 'parent_state']);
            })
            ->visible(function (Move $record) {
                return
                    $record->state == MoveState::CANCEL
                    || $record->state == MoveState::POSTED;
            });
    }

    private function validateMove(Move &$record): bool
    {
        if (! in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])) {
            Notification::make()
                ->warning()
                ->title(__('accounts::filament/resources/invoice/actions/reset-to-draft-action.validation.notification.error.invalid-state.title'))
                ->body(__('accounts::filament/resources/invoice/actions/reset-to-draft-action.validation.notification.error.invalid-state.body'))
                ->send();

            return false;
        }

        return true;
    }
}
