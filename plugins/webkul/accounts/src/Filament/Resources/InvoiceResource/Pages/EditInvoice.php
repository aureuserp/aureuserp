<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Account\Models\MoveLine;
use Webkul\Partner\Models\Partner;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/invoice/pages/edit-invoice.notification.title'))
            ->body(__('accounts::filament/resources/invoice/pages/edit-invoice.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            BaseActions\PayAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\ResetToDraftAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            BaseActions\PreviewAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();

        $record = $this->getRecord();

        $data['partner_id'] = $data['partner_id'] ??= $record->partner_id;

        if ($data['partner_id']) {
            $partner = Partner::find($data['partner_id']);

            $data['invoice_partner_display_name'] = $partner->name;
        } else {
            $data['invoice_partner_display_name'] = "#Created By: {$user->name}";
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        $this->getResource()::collectTotals($record);

        $this->updateOrCreatePaymentTermLine($record);

        $this->updateOrCreateTaxLine($record);
    }

    private function updateOrCreatePaymentTermLine($record): void
    {
        $paymentTermLine = MoveLine::where('move_id', $record->id)
            ->where('display_type', 'payment_term')
            ->first();

        if ($record->invoicePaymentTerm && $record->invoicePaymentTerm?->dueTerm?->nb_days) {
            $dateMaturity = $record->invoice_date_due->addDays($record->invoicePaymentTerm->dueTerm->nb_days);
        } else {
            $dateMaturity = $record->invoice_date_due;
        }

        if ($paymentTermLine) {
            $paymentTermLine->update([
                'currency_id'           => $record->currency_id,
                'partner_id'            => $record->partner_id,
                'date_maturity'         => $dateMaturity,
                'company_id'            => $record->company_id,
                'company_currency_id'   => $record->company_currency_id,
                'commercial_partner_id' => $record->partner_id,
                'parent_state'          => $record->state,
                'debit'                 => $record->amount_total,
                'balance'               => $record->amount_total,
                'amount_currency'       => $record->amount_total,
            ]);
        } else {
            MoveLine::create([
                'move_id'               => $record->id,
                'move_name'             => $record->name,
                'display_type'          => 'payment_term',
                'currency_id'           => $record->currency_id,
                'partner_id'            => $record->partner_id,
                'date_maturity'         => $dateMaturity,
                'company_id'            => $record->company_id,
                'company_currency_id'   => $record->company_currency_id,
                'commercial_partner_id' => $record->partner_id,
                'sort'                  => MoveLine::max('sort') + 1,
                'parent_state'          => $record->state,
                'date'                  => now(),
                'creator_id'            => $record->creator_id,
                'debit'                 => $record->amount_total,
                'balance'               => $record->amount_total,
                'amount_currency'       => $record->amount_total,
            ]);
        }
    }

    private function updateOrCreateTaxLine($record): void
    {
        $lines = $record->lines->where('display_type', DisplayType::PRODUCT->value);

        foreach ($lines as $line) {
            if ($line->taxes->isEmpty()) {
                continue;
            }

            $taxes = $line->taxes()->orderBy('sort')->get();

            $baseAmount = $line->price_subtotal;

            $priceUnit = $line->price_unit;

            $quantity = $line->quantity;

            $taxesComputed = [];

            foreach ($taxes as $tax) {
                $amount = floatval($tax->amount);

                $currentTaxBase = $baseAmount;

                $tax->price_include_override ??= 'tax_excluded';

                if ($tax->is_base_affected) {
                    foreach ($taxesComputed as $prevTax) {
                        if ($prevTax['include_base_amount']) {
                            $currentTaxBase += $prevTax['tax_amount'];
                        }
                    }
                }

                $currentTaxAmount = 0;

                if ($tax->price_include_override == 'tax_included') {
                    $taxFactor = ($tax->amount_type == 'percent') ? $amount / 100 : $amount;

                    $currentTaxAmount = $currentTaxBase - ($currentTaxBase / (1 + $taxFactor));

                    if (empty($taxesComputed)) {
                        $priceUnit = $priceUnit - ($currentTaxAmount / $quantity);

                        $subTotal = $priceUnit * $quantity;

                        $baseAmount = $subTotal;
                    }
                } elseif ($tax->price_include_override == 'tax_excluded') {
                    if ($tax->amount_type == 'percent') {
                        $currentTaxAmount = $currentTaxBase * $amount / 100;
                    } else {
                        $currentTaxAmount = $amount * $quantity;
                    }
                }

                $existingTaxLine = MoveLine::where([
                    'move_id' => $record->id,
                    'display_type' => 'tax',
                    'name' => $tax->name,
                ])->first();

                $taxLineData = [
                    'name'                  => $tax->name,
                    'move_id'               => $record->id,
                    'move_name'             => $record->name,
                    'display_type'          => 'tax',
                    'currency_id'           => $record->currency_id,
                    'partner_id'            => $record->partner_id,
                    'company_id'            => $record->company_id,
                    'company_currency_id'   => $record->company_currency_id,
                    'commercial_partner_id' => $record->partner_id,
                    'parent_state'          => $record->state,
                    'date'                  => now(),
                    'creator_id'            => $record->creator_id,
                    'debit'                 => $currentTaxAmount,
                    'credit'                => $currentTaxAmount,
                    'balance'               => -$currentTaxAmount,
                    'amount_currency'       => -$currentTaxAmount,
                    'tax_base_amount'       => $currentTaxBase,
                ];

                if ($existingTaxLine) {
                    $existingTaxLine->update($taxLineData);
                } else {
                    $taxLineData['sort'] = MoveLine::max('sort') + 1;
                    MoveLine::create($taxLineData);
                }
            }
        }
    }
}
