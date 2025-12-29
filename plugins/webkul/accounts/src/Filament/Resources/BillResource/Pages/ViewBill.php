<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Account\Models\Move;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Support\Filament\Concerns\HasRepeatableEntryColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewBill extends ViewRecord
{
    use HasRecordNavigationTabs, HasRepeatableEntryColumnManager;

    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->setResource($this->getResource()),
            BaseActions\PayAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\ResetToDraftAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            BaseActions\ReverseAction::make()
                ->label(__('accounts::filament/resources/bill/pages/view-bill.header-actions.reverse.label'))
                ->modalHeading(__('accounts::filament/resources/bill/pages/view-bill.header-actions.reverse.modal-heading')),
            DeleteAction::make()
                ->hidden(fn (Move $record): bool => $record->state == MoveState::POSTED)
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/bill/pages/view-bill.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/bill/pages/view-bill.header-actions.delete.notification.body'))
                ),
        ];
    }
}
