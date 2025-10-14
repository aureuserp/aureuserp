<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource;
use Webkul\Purchase\Models\Requisition;
use Webkul\Support\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditPurchaseAgreement extends EditRecord
{
    use HasRecordNavigationTabs;
    use HasRepeaterColumnManager;

    protected static string $resource = PurchaseAgreementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/edit-purchase-agreement.notification.title'))
            ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/edit-purchase-agreement.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->setResource(static::$resource),
            Action::make('confirm')
                ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/edit-purchase-agreement.header-actions.confirm.label'))
                ->color('primary')
                ->action(function () {
                    $this->getRecord()->update([
                        'state' => RequisitionState::CONFIRMED,
                    ]);

                    $this->fillForm();
                })
                ->visible(fn () => $this->getRecord()->state == RequisitionState::DRAFT),
            Action::make('close')
                ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/edit-purchase-agreement.header-actions.close.label'))
                ->color('primary')
                ->action(function () {
                    $this->getRecord()->update([
                        'state' => RequisitionState::CLOSED,
                    ]);

                    $this->fillForm();
                })
                ->visible(fn () => $this->getRecord()->state == RequisitionState::CONFIRMED),
            Action::make('cancelRecord')
                ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/edit-purchase-agreement.header-actions.cancel.label'))
                ->color('gray')
                ->action(function () {
                    $this->getRecord()->update([
                        'state' => RequisitionState::CANCELED,
                    ]);

                    $this->fillForm();
                })
                ->visible(fn () => ! in_array($this->getRecord()->state, [
                    RequisitionState::CLOSED,
                    RequisitionState::CANCELED,
                ])),
            Action::make('print')
                ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/edit-purchase-agreement.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function (Requisition $record) {
                    $pdf = PDF::loadView('purchases::filament.admin.clusters.orders.purchase-agreements.print', [
                        'records' => collect([$record]),
                    ]);

                    $pdf->setPaper('a4', 'portrait');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'Purchase Agreement-'.str_replace('/', '_', $record->name).'.pdf');
                }),
            DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->state == RequisitionState::CLOSED)
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/edit-purchase-agreement.header-actions.delete.notification.title'))
                        ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/edit-purchase-agreement.header-actions.delete.notification.body')),
                ),
        ];
    }
}
