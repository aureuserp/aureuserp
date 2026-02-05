<?php

namespace Webkul\Account\Filament\Resources\RefundResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill as ViewRecord;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Account\Filament\Resources\RefundResource;
use Webkul\Account\Models\Move;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewRefund extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = RefundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource),
            BaseActions\PreviewAction::make()
                ->setTemplate('accounts::refund/actions/preview.index'),
            BaseActions\PayAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            BaseActions\ResetToDraftAction::make(),
            DeleteAction::make()
                ->hidden(fn (Move $record): bool => $record->state == MoveState::POSTED)
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/refund/pages/view-refund.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/refund/pages/view-refund.header-actions.delete.notification.body'))
                ),
        ];
    }
}
