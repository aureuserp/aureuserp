<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;

class CancelQuotationAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'orders.sales.cancel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color('gray')
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.title'))
            ->modalIcon('heroicon-s-x-circle')
            ->modalHeading(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.modal.heading'))
            ->modalDescription(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.modal.description'))
            ->modalFooterActions(function ($record, $livewire): array {
                return [
                    Action::make('sendAndCancel')
                        ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.send-and-cancel.title'))
                        ->icon('heroicon-o-envelope')
                        ->modalIcon('heroicon-s-envelope')
                        ->action(function ($data) use ($record, $livewire) {
                            SaleOrder::cancelSaleOrder($record, $livewire->mountedActionsData[0] ?? []);

                            $livewire->refreshFormData(['state']);

                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.send-and-cancel.notification.cancelled.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.send-and-cancel.notification.cancelled.body'))
                                ->send();
                        })
                        ->cancelParentActions(),
                    Action::make('cancel')
                        ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.cancel.title'))
                        ->icon('heroicon-o-x-circle')
                        ->modalIcon('heroicon-s-x-circle')
                        ->action(function () use ($record, $livewire) {
                            SaleOrder::cancelSaleOrder($record);

                            $livewire->refreshFormData(['state']);

                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.cancel.notification.cancelled.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.cancel.notification.cancelled.body'))
                                ->send();
                        })
                        ->cancelParentActions(),
                    Action::make('close')
                        ->color('gray')
                        ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.close.title'))
                        ->cancelParentActions(),
                ];
            })
            ->form(
                function (Form $form, $record) {
                    return $form->schema([
                        Forms\Components\Select::make('partners')
                            ->options(Partner::all()->pluck('name', 'id'))
                            ->multiple()
                            ->default([$record->partner_id])
                            ->searchable()
                            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.partner'))
                            ->preload(),
                        Forms\Components\TextInput::make('subject')
                            ->default(fn () => __('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.subject-default', [
                                'name' => $record->name,
                                'id'   => $record->id,
                            ]))
                            ->placeholder(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.subject-placeholder'))
                            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.subject'))
                            ->hiddenLabel(),
                        Forms\Components\RichEditor::make('description')
                            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.description'))
                            ->default(function () use ($record) {
                                return __('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.description-default', [
                                    'partner_name' => $record?->partner?->name,
                                    'name'         => $record?->name,
                                ]);
                            })
                            ->hiddenLabel(),
                    ]);
                }
            )
            ->hidden(fn ($record) => ! in_array($record->state, [OrderState::DRAFT, OrderState::SENT, OrderState::SALE]));
    }
}
