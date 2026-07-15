<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;

class CancelAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

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
                try {
                    $record = AccountFacade::cancelMove($record);

                    $livewire->refreshFormData(['state', 'parent_state']);

                    $livewire->dispatch('refreshInvoiceSummary');
                } catch (Throwable $e) {
                    Notification::make()
                        ->warning()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->hidden(fn (Move $record) => $record->state != MoveState::DRAFT);
    }
}
