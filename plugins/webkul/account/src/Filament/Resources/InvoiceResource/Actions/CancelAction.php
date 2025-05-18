<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account;
use Webkul\Account\Models\Move;

class CancelAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.cancel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/cancel-action.title'))
            ->color('gray')
            ->action(function (Move $record, Component $livewire): void {
                $record = Account::cancel($record);

                $livewire->refreshFormData(['state', 'parent_state']);
            })
            ->hidden(function (Move $record) {
                return
                    $record->state != MoveState::DRAFT
                    || $record->move_type == MoveType::ENTRY;
            });
    }
}
