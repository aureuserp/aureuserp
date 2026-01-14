<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Account\Filament\Resources\RefundResource;
use Webkul\Account\Models\Move;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditBill extends EditRecord
{
    use HasRecordNavigationTabs, HasRepeaterColumnManager;

    protected static string $resource = BillResource::class;

    protected static string $reverseResource = RefundResource::class;

    /**
     * @return class-string
     */
    public static function getReverseResource(): string
    {
        return static::$reverseResource;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/bill/pages/edit-bill.notification.title'))
            ->body(__('accounts::filament/resources/bill/pages/edit-bill.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource($this->getResource()),
            BaseActions\PreviewAction::make()
                ->setTemplate('accounts::bill/actions/preview.index'),
            BaseActions\PayAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            BaseActions\ReverseAction::make()
                ->label(__('accounts::filament/resources/bill/pages/edit-bill.header-actions.reverse.label'))
                ->modalHeading(__('accounts::filament/resources/bill/pages/edit-bill.header-actions.reverse.modal-heading')),
            BaseActions\ResetToDraftAction::make(),
            DeleteAction::make()
                ->hidden(fn (Move $record): bool => $record->state == MoveState::POSTED)
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/bill/pages/edit-bill.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/bill/pages/edit-bill.header-actions.delete.notification.body'))
                ),
        ];
    }

    protected function afterSave(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());
    }
}
