<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Throwable;
use Webkul\PointOfSale\Enums\PaymentMethodType;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;

class RefundOrderAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'point-of-sale.order.refund';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(fn (Order $record): string => $this->hasRefundableLines($record)
                ? __('point-of-sale::filament/admin/clusters/orders/actions/refund-order.label')
                : __('point-of-sale::filament/admin/clusters/orders/actions/refund-order.refunded-label'))
            ->disabled(fn (Order $record): bool => ! $this->hasRefundableLines($record))
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('danger')
            ->modalWidth('2xl')
            ->fillForm(fn (Order $record): array => [
                'payment_method_id' => $record->config?->paymentMethods
                    ->firstWhere('type', PaymentMethodType::CASH)?->id
                    ?? $record->config?->paymentMethods->first()?->id,
                'lines' => $record->lines
                    ->filter(fn (OrderLine $line): bool => float_compare($line->refundableQty(), 0, precisionDigits: 4) > 0)
                    ->map(fn (OrderLine $line): array => [
                        'line_id'     => $line->id,
                        'is_selected' => true,
                        'product'     => $line->full_product_name ?? $line->product?->name,
                        'refundable'  => $line->refundableQty(),
                        'quantity'    => $line->refundableQty(),
                    ])
                    ->values()
                    ->all(),
            ])
            ->schema([
                Select::make('payment_method_id')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/actions/refund-order.form.fields.payment-method'))
                    ->options(fn (Order $record): array => $record->config?->paymentMethods
                        ->pluck('name', 'id')
                        ->all() ?? [])
                    ->required()
                    ->columnSpanFull(),

                Repeater::make('lines')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/actions/refund-order.form.fields.lines'))
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->compact()
                    ->table([
                        TableColumn::make('is_selected')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/actions/refund-order.form.fields.selected')),

                        TableColumn::make('product')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/actions/refund-order.form.fields.product')),

                        TableColumn::make('refundable')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/actions/refund-order.form.fields.refundable')),

                        TableColumn::make('quantity')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/actions/refund-order.form.fields.quantity')),
                    ])
                    ->schema([
                        Hidden::make('line_id'),

                        Checkbox::make('is_selected')
                            ->hiddenLabel()
                            ->live()
                            ->extraFieldWrapperAttributes(['class' => 'px-3']),

                        TextEntry::make('product')
                            ->hiddenLabel(),

                        TextEntry::make('refundable')
                            ->hiddenLabel()
                            ->numeric(decimalPlaces: 4),

                        TextInput::make('quantity')
                            ->hiddenLabel()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(fn (Get $get): float => (float) $get('refundable'))
                            ->required(fn (Get $get): bool => (bool) $get('is_selected'))
                            ->disabled(fn (Get $get): bool => ! $get('is_selected')),
                    ])
                    ->columnSpanFull(),
            ])
            ->action(function (Order $record, array $data): void {
                try {
                    $quantities = collect($data['lines'] ?? [])
                        ->filter(fn (array $line): bool => (bool) ($line['is_selected'] ?? false))
                        ->mapWithKeys(fn (array $line): array => [
                            (int) $line['line_id'] => (float) ($line['quantity'] ?? 0),
                        ])
                        ->all();

                    $refund = PointOfSale::refundOrder($record, $quantities);

                    PointOfSale::settleRefund($refund, (int) $data['payment_method_id']);

                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/orders/actions/refund-order.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/orders/actions/refund-order.notification.success.body'))
                        ->send();
                } catch (Throwable $exception) {
                    Notification::make()
                        ->danger()
                        ->body($exception->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(fn (Order $record): bool => $record->state->isSettled() && ! $record->isRefund());
    }

    protected function hasRefundableLines(Order $record): bool
    {
        return $record->lines->contains(fn (OrderLine $line): bool => float_compare($line->refundableQty(), 0, precisionDigits: 4) > 0);
    }
}
