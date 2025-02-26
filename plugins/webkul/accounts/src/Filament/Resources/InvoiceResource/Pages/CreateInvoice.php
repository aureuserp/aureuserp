<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/invoice/pages/create-invoice.notification.title'))
            ->body(__('accounts::filament/resources/invoice/pages/create-invoice.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $data['creator_id'] = $user->id;
        $data['state'] ??= Enums\MoveState::DRAFT->value;
        $data['move_type'] ??= Enums\MoveType::OUT_INVOICE->value;
        $data['date'] = now();
        $data['sort'] = Move::max('sort') + 1;

        if ($data['partner_id']) {
            $partner = Partner::find($data['partner_id']);

            $data['invoice_partner_display_name'] = $partner->name;
        } else {
            $data['invoice_partner_display_name'] = "#Created By: {$user->name}";
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        $this->getResource()::collectTotals($record);

        $this->createPaymentTermLine($record);

        $this->createTaxLine($record);
    }

    private function createPaymentTermLine($record): void
    {
        if ($record->invoicePaymentTerm && $record->invoicePaymentTerm?->dueTerm?->nb_days) {
            $dateMaturity = $record->invoice_date_due->addDays($record->invoicePaymentTerm->dueTerm->nb_days);
        } else {
            $dateMaturity = $record->invoice_date_due;
        }

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

    private function createTaxLine($record): void
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

                $taxesComputed[] = [
                    'tax_id'              => $tax->id,
                    'tax_amount'          => $currentTaxAmount,
                    'include_base_amount' => $tax->include_base_amount,
                ];

                MoveLine::create([
                    'name'                  => $tax->name,
                    'move_id'               => $record->id,
                    'move_name'             => $record->name,
                    'display_type'          => 'tax',
                    'currency_id'           => $record->currency_id,
                    'partner_id'            => $record->partner_id,
                    'company_id'            => $record->company_id,
                    'company_currency_id'   => $record->company_currency_id,
                    'commercial_partner_id' => $record->partner_id,
                    'sort'                  => MoveLine::max('sort') + 1,
                    'parent_state'          => $record->state,
                    'date'                  => now(),
                    'creator_id'            => $record->creator_id,
                    'debit'                 => 0.0,
                    'credit'                => $currentTaxAmount,
                    'balance'               => -$currentTaxAmount,
                    'amount_currency'       => -$currentTaxAmount,
                    'tax_base_amount'       => $currentTaxBase,
                ]);
            }
        }
    }
}
