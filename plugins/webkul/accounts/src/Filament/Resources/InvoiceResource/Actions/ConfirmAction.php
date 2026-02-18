<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Exception;
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
            ->color('primary')
            ->action(function (Move $record, Component $livewire): void {
                $record->checked = $record->journal->auto_check_on_post;

                try {
                    $record = AccountFacade::confirmMove($record);

                    $livewire->refreshFormData(['state', 'parent_state']);
                } catch (Exception $e) {
                    Notification::make()
                        ->warning()
                        ->title('Confirmation Error')
                        ->body($e->getMessage())
                        ->send();
                }
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
}
