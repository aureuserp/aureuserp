<?php

use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\DueTermValue;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Enums\RoundingMethod;
use Webkul\Account\Enums\RoundingStrategy;
use Webkul\Account\Models\CashRounding;
use Webkul\Account\Models\PaymentDueTerm;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\FiscalPositionAccount;
use Webkul\Account\Models\FiscalPositionTax;
use Webkul\Account\Models\Journal;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\PaymentMethodLine;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\CurrencyRate;
use Webkul\Support\Models\UOM;

class AccountHelper
{
    public static function company(): Company
    {
        return Company::query()->firstOrFail();
    }

    public static function actingAsAdmin(): User
    {
        $user = User::query()->firstOrFail();

        Auth::login($user);

        return $user;
    }

    public static function unitsUom(): UOM
    {
        return UOM::query()->where('name', 'Units')->firstOrFail();
    }

    public static function currency(): Currency
    {
        return Currency::query()->firstOrFail();
    }

    public static function account(string $type = 'income'): Account
    {
        return Account::factory()->{$type}()->create([
            'currency_id' => static::currency()->id,
        ]);
    }

    public static function saleJournal(): Journal
    {
        return Journal::factory()->sale()->create([
            'company_id'         => static::company()->id,
            'currency_id'        => static::currency()->id,
            'default_account_id' => static::account('income')->id,
        ]);
    }

    public static function purchaseJournal(): Journal
    {
        return Journal::factory()->purchase()->create([
            'company_id'         => static::company()->id,
            'currency_id'        => static::currency()->id,
            'default_account_id' => static::account('expense')->id,
        ]);
    }

    public static function generalJournal(): Journal
    {
        return Journal::factory()->create([
            'type'               => JournalType::GENERAL,
            'company_id'         => static::company()->id,
            'currency_id'        => static::currency()->id,
            'default_account_id' => static::account('income')->id,
        ]);
    }

    public static function partner(): Partner
    {
        return Partner::factory()->create([
            'property_account_receivable_id' => static::account('receivable')->id,
            'property_account_payable_id'    => static::account('payable')->id,
        ]);
    }

    public static function product(array $overrides = []): Product
    {
        $uom = static::unitsUom();

        return Product::factory()->create(array_merge([
            'uom_id'     => $uom->id,
            'uom_po_id'  => $uom->id,
            'company_id' => static::company()->id,
        ], $overrides));
    }

    public static function tax(
        float $amount = 10,
        AmountType $amountType = AmountType::PERCENT,
        TaxIncludeOverride $include = TaxIncludeOverride::TAX_EXCLUDED,
        TypeTaxUse $type = TypeTaxUse::SALE,
    ): Tax {
        return Tax::factory()->create([
            'amount'                 => $amount,
            'amount_type'            => $amountType,
            'price_include_override' => $include,
            'type_tax_use'           => $type,
            'company_id'             => static::company()->id,
        ]);
    }

    public static function invoice(MoveType $type = MoveType::OUT_INVOICE, ?Partner $partner = null, ?Journal $journal = null, array $overrides = []): Move
    {
        $partner ??= static::partner();

        $journal ??= in_array($type, [MoveType::IN_INVOICE, MoveType::IN_REFUND])
            ? static::purchaseJournal()
            : static::saleJournal();

        return Move::factory()->create(array_merge([
            'move_type'        => $type,
            'state'            => MoveState::DRAFT,
            'journal_id'       => $journal->id,
            'partner_id'       => $partner->id,
            'currency_id'      => static::currency()->id,
            'company_id'       => static::company()->id,
            'invoice_date'     => now()->toDateString(),
            'invoice_date_due' => now()->toDateString(),
            'date'             => now()->toDateString(),
        ], $overrides));
    }

    public static function journalEntry(?Journal $journal = null, array $overrides = []): Move
    {
        $journal ??= static::generalJournal();

        return Move::factory()->create(array_merge([
            'move_type'   => MoveType::ENTRY,
            'state'       => MoveState::DRAFT,
            'journal_id'  => $journal->id,
            'currency_id' => static::currency()->id,
            'company_id'  => static::company()->id,
            'date'        => now()->toDateString(),
        ], $overrides));
    }

    public static function entryLine(Move $move, Account $account, float $debit, float $credit): MoveLine
    {
        return MoveLine::factory()->create([
            'move_id'         => $move->id,
            'account_id'      => $account->id,
            'debit'           => $debit,
            'credit'          => $credit,
            'balance'         => $debit - $credit,
            'amount_currency' => $debit - $credit,
            'currency_id'     => $move->currency_id,
            'company_id'      => static::company()->id,
        ]);
    }

    public static function displayLine(Move $move, DisplayType $type, string $name): MoveLine
    {
        return MoveLine::factory()->create([
            'move_id'         => $move->id,
            'display_type'    => $type,
            'account_id'      => null,
            'name'            => $name,
            'quantity'        => 0,
            'price_unit'      => 0,
            'price_subtotal'  => 0,
            'price_total'     => 0,
            'debit'           => 0,
            'credit'          => 0,
            'balance'         => 0,
            'amount_currency' => 0,
            'currency_id'     => $move->currency_id,
            'company_id'      => static::company()->id,
        ]);
    }

    public static function setAsChecked(Move $move): Move
    {
        return AccountFacade::setAsCheckedMove($move->refresh());
    }

    public static function productLine(Move $move, Account $account, float $qty, float $priceUnit, float $discount = 0, array $taxes = []): MoveLine
    {
        $line = MoveLine::factory()->create([
            'move_id'      => $move->id,
            'display_type' => DisplayType::PRODUCT,
            'account_id'   => $account->id,
            'product_id'   => static::product()->id,
            'uom_id'       => static::unitsUom()->id,
            'quantity'     => $qty,
            'price_unit'   => $priceUnit,
            'discount'     => $discount,
            'currency_id'  => $move->currency_id,
            'company_id'   => static::company()->id,
        ]);

        if ($taxes) {
            $line->taxes()->attach(collect($taxes)->pluck('id')->all());
        }

        return $line->refresh();
    }

    public static function taxWithAccounts(
        float $amount = 10,
        AmountType $amountType = AmountType::PERCENT,
        TypeTaxUse $type = TypeTaxUse::SALE,
        TaxIncludeOverride $include = TaxIncludeOverride::TAX_EXCLUDED,
        array $taxFactors = [100],
    ): Tax {
        $tax = static::tax($amount, $amountType, $include, $type);

        $taxAccount = Account::factory()->create([
            'account_type' => AccountType::LIABILITY_CURRENT,
            'currency_id'  => static::currency()->id,
        ]);

        foreach ([DocumentType::INVOICE, DocumentType::REFUND] as $document) {
            TaxPartition::factory()->create([
                'tax_id'           => $tax->id,
                'document_type'    => $document,
                'repartition_type' => RepartitionType::BASE,
                'factor_percent'   => 100,
                'account_id'       => null,
                'company_id'       => static::company()->id,
            ]);

            foreach ($taxFactors as $factor) {
                TaxPartition::factory()->create([
                    'tax_id'           => $tax->id,
                    'document_type'    => $document,
                    'repartition_type' => RepartitionType::TAX,
                    'factor_percent'   => $factor,
                    'account_id'       => $taxAccount->id,
                    'company_id'       => static::company()->id,
                ]);
            }
        }

        return $tax->refresh();
    }

    public static function groupTax(array $children, TypeTaxUse $type = TypeTaxUse::SALE): Tax
    {
        $parent = static::tax(0, AmountType::GROUP, TaxIncludeOverride::TAX_EXCLUDED, $type);

        $parent->childrenTaxes()->sync(collect($children)->pluck('id')->all());

        return $parent->refresh();
    }

    public static function earlyPaymentTerm(float $discountPercent = 2, int $discountDays = 7, int $dueDays = 30): PaymentTerm
    {
        $term = PaymentTerm::factory()->create([
            'company_id'          => static::company()->id,
            'early_discount'      => true,
            'discount_percentage' => $discountPercent,
            'discount_days'       => $discountDays,
        ]);

        $term->dueTerms()->delete();

        PaymentDueTerm::factory()->create([
            'payment_id'   => $term->id,
            'value'        => DueTermValue::PERCENT,
            'value_amount' => 100,
            'delay_type'   => DelayType::DAYS_AFTER,
            'nb_days'      => $dueDays,
        ]);

        return $term->refresh();
    }

    public static function bankJournal(): Journal
    {
        $journal = Journal::factory()->bank()->create([
            'company_id'         => static::company()->id,
            'currency_id'        => static::currency()->id,
            'default_account_id' => Account::factory()->create([
                'account_type' => AccountType::ASSET_CASH,
                'currency_id'  => static::currency()->id,
            ])->id,
        ]);

        foreach (Journal::getDefaultInboundPaymentMethodLines() as $data) {
            PaymentMethodLine::create(array_merge($data, ['journal_id' => $journal->id]));
        }

        foreach (Journal::getDefaultOutboundPaymentMethodLines() as $data) {
            PaymentMethodLine::create(array_merge($data, ['journal_id' => $journal->id]));
        }

        return $journal->refresh();
    }

    public static function pay(Move $move, ?float $amount = null, string $differenceHandling = 'open'): Move
    {
        static::bankJournal();

        $move = $move->refresh();

        $register = new PaymentRegister;
        $register->lines = $move->lines;
        $register->company = $move->company;
        $register->currency = $move->currency;
        $register->currency_id = $move->currency_id;
        $register->payment_type = $move->isInbound(true) ? PaymentType::RECEIVE : PaymentType::SEND;
        $register->computeBatches();
        $register->computeAvailableJournalIds();
        $register->journal_id = $register->available_journal_ids[0] ?? null;
        $register->journal = Journal::find($register->journal_id);
        $register->computePaymentMethodLineId();

        $amounts = $register->getTotalAmountsToPay($register->batches);

        $lineIds = $move->paymentTermLines
            ->filter(fn ($line) => ! $line->reconciled)
            ->pluck('id')
            ->toArray();

        $paymentRegister = PaymentRegister::create([
            'currency_id'                 => $move->currency_id,
            'journal_id'                  => $register->journal_id,
            'payment_method_line_id'      => $register->payment_method_line_id,
            'company_id'                  => $move->company_id,
            'partner_id'                  => $move->partner_id,
            'payment_type'                => $register->payment_type,
            'payment_date'                => now()->toDateString(),
            'amount'                      => $amount ?? $amounts['amount_by_default'],
            'installments_mode'           => $register->installments_mode ?? 'full',
            'payment_difference_handling' => $differenceHandling,
            'writeoff_account_id'         => $differenceHandling === 'reconcile' ? static::account('expense')->id : null,
            'writeoff_label'              => $differenceHandling === 'reconcile' ? 'Write-off' : null,
            'communication'               => $move->name,
        ]);

        $paymentRegister->lines()->sync($lineIds);

        $paymentRegister->refresh();

        $paymentRegister->computeFromLines();

        $paymentRegister->save();

        AccountFacade::createPayments($paymentRegister);

        return $move->refresh();
    }

    public static function otherCurrency(): Currency
    {
        return Currency::query()->where('id', '!=', static::currency()->id)->firstOrFail();
    }

    public static function setCurrencyRate(Currency $currency, float $rate, ?string $date = null): CurrencyRate
    {
        return CurrencyRate::factory()->create([
            'currency_id' => $currency->id,
            'company_id'  => static::company()->id,
            'rate'        => $rate,
            'name'        => $date ?? now()->subYears(5)->toDateString(),
        ]);
    }

    public static function paymentTerm(array $installments): PaymentTerm
    {
        $term = PaymentTerm::factory()->create([
            'company_id'         => static::company()->id,
            'early_discount'     => false,
            'early_pay_discount' => false,
        ]);

        $term->dueTerms()->delete();

        foreach ($installments as [$percent, $nbDays]) {
            PaymentDueTerm::factory()->create([
                'payment_id'   => $term->id,
                'value'        => DueTermValue::PERCENT,
                'value_amount' => $percent,
                'delay_type'   => DelayType::DAYS_AFTER,
                'nb_days'      => $nbDays,
            ]);
        }

        return $term->refresh();
    }

    public static function cashRounding(float $rounding = 0.05, RoundingStrategy $strategy = RoundingStrategy::ADD_INVOICE_LINE): CashRounding
    {
        return CashRounding::factory()->create([
            'rounding'          => $rounding,
            'strategy'          => $strategy,
            'rounding_method'   => RoundingMethod::HALF_UP,
            'profit_account_id' => static::account('income')->id,
            'loss_account_id'   => static::account('expense')->id,
        ]);
    }

    public static function fiscalPositionRemappingAccount(Account $source, Account $destination): FiscalPosition
    {
        $fiscalPosition = FiscalPosition::factory()->create(['company_id' => static::company()->id]);

        FiscalPositionAccount::factory()
            ->mapping($source->id, $destination->id)
            ->create([
                'fiscal_position_id' => $fiscalPosition->id,
                'company_id'         => static::company()->id,
            ]);

        return $fiscalPosition->refresh();
    }

    public static function fiscalPositionRemappingTax(Tax $source, Tax $destination): FiscalPosition
    {
        $fiscalPosition = FiscalPosition::factory()->create(['company_id' => static::company()->id]);

        FiscalPositionTax::factory()
            ->mapping($source->id, $destination->id)
            ->create([
                'fiscal_position_id' => $fiscalPosition->id,
                'company_id'         => static::company()->id,
            ]);

        return $fiscalPosition->refresh();
    }

    public static function reconcile(Move $a, Move $b): void
    {
        $lines = $a->refresh()->lines
            ->merge($b->refresh()->lines)
            ->filter(fn ($line) => in_array($line->account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE])
                && ! $line->reconciled)
            ->values();

        AccountFacade::reconcile($lines);
    }

    public static function unreconcile(Move $move): void
    {
        $line = $move->refresh()->lines
            ->firstWhere(fn ($line) => in_array($line->account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE]));

        $partial = $line->matchedDebits->merge($line->matchedCredits)->first();

        AccountFacade::unReconcile($partial);
    }

    public static function compute(Move $move): Move
    {
        return AccountFacade::computeAccountMove($move->refresh());
    }

    public static function post(Move $move): Move
    {
        return AccountFacade::confirmMove($move->refresh());
    }

    public static function cancel(Move $move): Move
    {
        return AccountFacade::cancelMove($move->refresh());
    }

    public static function resetToDraft(Move $move): Move
    {
        return AccountFacade::resetToDraftMove($move->refresh());
    }

    public static function reverse(Move $move): Move
    {
        return AccountFacade::reverseMoves(collect([$move->refresh()]))->first();
    }

    public static function reverseAndCancel(Move $move): Move
    {
        return AccountFacade::reverseMoves(collect([$move->refresh()]), [], true)->first();
    }
}
