<?php

namespace Webkul\Account;

use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;

class AccountManager
{
    public function computeAccountMove(AccountMove $record): void
    {
        $record->amount_untaxed = 0;
        $record->amount_tax = 0;
        $record->amount_total = 0;
        $record->amount_residual = 0;
        $record->amount_untaxed_signed = 0;
        $record->amount_untaxed_in_currency_signed = 0;
        $record->amount_tax_signed = 0;
        $record->amount_total_signed = 0;
        $record->amount_total_in_currency_signed = 0;
        $record->amount_residual_signed = 0;
        $newTaxEntries = [];

        foreach ($record->lines as $line) {
            $line->parent_state = $record->state;

            [$line, $amountTax] = $this->collectLineTotals($line, $newTaxEntries);

            $record->amount_untaxed += floatval($line->price_subtotal);
            $record->amount_tax += floatval($amountTax);
            $record->amount_total += floatval($line->price_total);

            $record->amount_untaxed_signed += floatval($line->price_subtotal);
            $record->amount_untaxed_in_currency_signed += floatval($line->price_subtotal);
            $record->amount_tax_signed += floatval($amountTax);
            $record->amount_total_signed += floatval($line->price_total);
            $record->amount_total_in_currency_signed += floatval($line->price_total);

            $record->amount_residual += floatval($line->price_total);
            $record->amount_residual_signed += floatval($line->price_total);
        }

        $record->save();

        static::updateOrCreatePaymentTermLine($record);
    }

    private function collectLineTotals(MoveLine $line, array &$newTaxEntries): array
    {
        $subTotal = $line->price_unit * $line->quantity;

        $discountAmount = 0;

        if ($line->discount > 0) {
            $discountAmount = $subTotal * ($line->discount / 100);

            $subTotal = $subTotal - $discountAmount;
        }

        $taxIds = $line->taxes->pluck('id')->toArray();

        [$subTotal, $taxAmount, $taxesComputed] = Tax::collect($taxIds, $subTotal, $line->quantity);

        if ($taxAmount > 0) {
            static::updateOrCreateTaxLine($line, $subTotal, $taxesComputed, $newTaxEntries);
        }

        $line->price_subtotal = round($subTotal, 4);

        $line->price_total = $subTotal + $taxAmount;

        $line = $this->computeBalance($line);

        $line = $this->computeCreditAndDebit($line);

        $line = $this->computeAmountCurrency($line);

        $line->save();

        return [
            $line,
            $taxAmount,
        ];
    }

    private function computeCreditAndDebit(MoveLine $line): MoveLine
    {
        $move = $line->move;

        if (! $move->is_storno) {
            $line->debit = $line->balance > 0.0 ? $line->balance : 0.0;
            $line->credit = $line->balance < 0.0 ? -$line->balance : 0.0;
        } else {
            $line->debit = $line->balance < 0.0 ? $line->balance : 0.0;
            $line->credit = $line->balance > 0.0 ? -$line->balance : 0.0;
        }

        return $line;
    }

    private function computeBalance(MoveLine $line): MoveLine
    {
        if (in_array($line->display_type, [DisplayType::LINE_SECTION, DisplayType::LINE_NOTE])) {
            $line->balance = false;
        } elseif ($line->move->isInvoice(true)) {
            $move = $line->move;

            $otherLines = $move->lines()->where('id', '!=', $move->id)->get();

            $line->balance = -$otherLines->sum('balance');
        } else {
            $line->balance = 0.0000;
        }

        return $line;
    }

    private function computeAmountCurrency(MoveLine $line): MoveLine
    {
        if (is_null($line->amount_currency)) {
            $line->amount_currency = round($line->balance * $line->currency_rate, 2);
        }

        if ($line->currency_id === $line->company->currency_id) {
            $line->amount_currency = $line->balance;
        }

        return $line;
    }

    private function preparePaymentTermLine(AccountMove $move): array
    {
        $dateMaturity = $move->invoice_date_due;

        if (
            $move->invoicePaymentTerm
            && $move->invoicePaymentTerm->dueTerm?->nb_days
        ) {
            $dateMaturity = $dateMaturity->addDays($move->invoicePaymentTerm->dueTerm->nb_days);
        }

        return [
            'move_id'                  => $move->id,
            'move_name'                => $move->name,
            'display_type'             => 'payment_term',
            'currency_id'              => $move->currency_id,
            'partner_id'               => $move->partner_id,
            'date_maturity'            => $dateMaturity,
            'company_id'               => $move->company_id,
            'company_currency_id'      => $move->company_currency_id,
            'commercial_partner_id'    => $move->partner_id,
            'sort'                     => MoveLine::max('sort') + 1,
            'parent_state'             => $move->state,
            'date'                     => now(),
            'creator_id'               => $move->creator_id,
            'debit'                    => $move->amount_total,
            'balance'                  => $move->amount_total,
            'amount_currency'          => $move->amount_total,
            'amount_residual'          => $move->amount_total,
            'amount_residual_currency' => $move->amount_total,
        ];
    }

    private function updateOrCreatePaymentTermLine($move): void
    {
        MoveLine::updateOrCreate(
            [
                'move_id'      => $move->id,
                'display_type' => 'payment_term',
            ],
            $this->preparePaymentTermLine($move)
        );
    }

    private static function updateOrCreateTaxLine($line, $subTotal, $taxesComputed, &$newTaxEntries): void
    {
        $taxes = $line
            ->taxes()
            ->orderBy('sort')
            ->get();

        $existingTaxLines = MoveLine::where('move_id', $line->move->id)
            ->where('display_type', 'tax')
            ->get()
            ->keyBy('tax_line_id');

        foreach ($taxes as $tax) {
            $move = $line->move;

            $computedTax = collect($taxesComputed)->firstWhere('tax_id', $tax->id);

            $currentTaxAmount = $computedTax['tax_amount'];

            if (isset($newTaxEntries[$tax->id])) {
                $newTaxEntries[$tax->id]['credit'] += $currentTaxAmount;
                $newTaxEntries[$tax->id]['balance'] -= $currentTaxAmount;
                $newTaxEntries[$tax->id]['tax_base_amount'] += $subTotal;
                $newTaxEntries[$tax->id]['amount_currency'] -= $currentTaxAmount;
            } else {
                $newTaxEntries[$tax->id] = [
                    'name'                  => $tax->name,
                    'move_id'               => $move->id,
                    'move_name'             => $move->name,
                    'display_type'          => 'tax',
                    'currency_id'           => $move->currency_id,
                    'partner_id'            => $move->partner_id,
                    'company_id'            => $move->company_id,
                    'company_currency_id'   => $move->company_currency_id,
                    'commercial_partner_id' => $move->partner_id,
                    'parent_state'          => $move->state,
                    'date'                  => now(),
                    'creator_id'            => $move->creator_id,
                    'debit'                 => 0,
                    'credit'                => $currentTaxAmount,
                    'balance'               => -$currentTaxAmount,
                    'amount_currency'       => -$currentTaxAmount,
                    'tax_base_amount'       => $subTotal,
                    'tax_line_id'           => $tax->id,
                    'tax_group_id'          => $tax->tax_group_id,
                ];
            }
        }

        foreach ($newTaxEntries as $taxId => $taxData) {
            if (isset($existingTaxLines[$taxId])) {
                $existingTaxLines[$taxId]->update($taxData);

                unset($existingTaxLines[$taxId]);
            } else {
                $taxData['sort'] = MoveLine::max('sort') + 1;

                MoveLine::create($taxData);
            }
        }

        $existingTaxLines->each->delete();
    }
}
