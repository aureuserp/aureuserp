<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Partner\Models\BankAccount;
use Webkul\Partner\Models\Partner;
use Webkul\Payment\Models\PaymentToken;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveType;
use Webkul\Payment\Models\PaymentTransaction;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Account\Settings\DefaultAccountSettings;

class Payment extends Model
{
    use HasChatter, HasFactory, HasLogActivity;

    protected $table = 'accounts_account_payments';

    protected $fillable = [
        'move_id',
        'journal_id',
        'company_id',
        'partner_bank_id',
        'paired_internal_transfer_payment_id',
        'payment_method_line_id',
        'payment_method_id',
        'currency_id',
        'partner_id',
        'outstanding_account_id',
        'destination_account_id',
        'created_by',
        'name',
        'state',
        'payment_type',
        'partner_type',
        'memo',
        'payment_reference',
        'date',
        'amount',
        'amount_company_currency_signed',
        'is_reconciled',
        'is_matched',
        'is_sent',
        'payment_transaction_id',
        'source_payment_id',
        'payment_token_id',
    ];

    public array $moveRelatedFields = [
        'partner_id',
        'currency_id',
        'partner_bank_id',
        'ref',
        'date',
    ];

    protected array $logAttributes = [
        'name',
        'move.name'          => 'Move',
        'company.name'       => 'Company',
        'partner.name'       => 'Partner',
        'partner_type'       => 'Partner Type',
        'paymentMethod.name' => 'Payment Method',
        'currency.name'      => 'Currency',
        'paymentToken',
        'sourcePayment.name'      => 'Source Payment',
        'paymentTransaction.name' => 'Payment Transaction',
        'destinationAccount.name' => 'Destination Account',
        'outstandingAccount.name' => 'Outstanding Account',
        'is_sent'                 => 'Is Sent',
        'state'                   => 'State',
    ];

    public function move()
    {
        return $this->belongsTo(Move::class, 'move_id');
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function partnerBank()
    {
        return $this->belongsTo(BankAccount::class, 'partner_bank_id')->withTrashed();
    }

    public function pairedInternalTransferPayment()
    {
        return $this->belongsTo(self::class, 'paired_internal_transfer_payment_id');
    }

    public function paymentMethodLine()
    {
        return $this->belongsTo(PaymentMethodLine::class, 'payment_method_line_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function outstandingAccount()
    {
        return $this->belongsTo(Account::class, 'outstanding_account_id');
    }

    public function destinationAccount()
    {
        return $this->belongsTo(Account::class, 'destination_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }

    public function sourcePayment()
    {
        return $this->belongsTo(self::class, 'source_payment_id');
    }

    public function paymentToken()
    {
        return $this->belongsTo(PaymentToken::class, 'payment_token_id');
    }

    public function invoices()
    {
        return $this->belongsToMany(Move::class, 'accounts_accounts_move_payment', 'payment_id', 'invoice_id');
    }

    public function getMethodCodesUsingBankAccount()
    {
        return ['manual'];
    }

    public function getMethodCodesNeedingBankAccount()
    {
        return [];
    }
    
    public function getValidLiquidityAccounts()
    {
        return collect([
            $this->journal->defaultAccount ?? null,
            $this->paymentMethodLine->paymentAccount ?? null,
        ])
        ->merge($this->journal->inboundPaymentMethodLines->pluck('paymentAccount'))
        ->merge($this->journal->outboundPaymentMethodLines->pluck('paymentAccount'))
        ->filter()
        ->unique('id')
        ->values();
    }

    //TODO: need to fetch company outstanding account based on payment type
    public function getOutstandingAccount($paymentType)
    {
        $transferAccountId = (new DefaultAccountSettings())->transfer_account_id;

        return Account::find($transferAccountId);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($payment) {
            $payment->computeName();

            $payment->computeState();

            $payment->computeCreatedBy();

            $payment->computeOutstandingAccountId();

            $payment->computeDestinationAccountId();

            $payment->computeAmountCompanyCurrencySigned();

            $payment->computeReconciliationStatus();
        });
    }

    //TODO:: needs to be updated to fetch name from payment method or journal
    public function computeName()
    {
        $this->name = $this->move?->name;
    }

    public function computeCreatedBy()
    {
        $this->created_by = filament()->auth()->id();
    }

    public function computeState()
    {
        if (! $this->state) {
            $this->state = 'draft';
        }

        if ($this->state === 'in_process' && $this->outstanding_account_id) {
            [$liquidity] = $this->seekForLines();

            if (
                $this->move
                && $this->move->currency->isZero($liquidity->sum('amount_residual'))
            ) {
                $this->state = 'paid';

                return;
            }
        }

        if (
            $this->state === 'in_process'
            && $this->invoices()->exists()
            && $this->invoices->every(fn($invoice) => $invoice->payment_state === 'paid')
        ) {
            $this->state = 'paid';
        }
    }

    public function computeReconciliationStatus()
    {
        if (! $this->move) {
            $this->is_reconciled = false;

            $this->is_matched = false;

            return;
        }

        [$liquidity, $counterpart, $writeoff] = $this->seekForLines();

        if (! $this->outstanding_account_id) {
            $this->is_reconciled = false;

            $this->is_matched = $this->state === 'paid';

            return;
        }

        if (! $this->currency_id || !$this->id || !$this->move_id) {
            $this->is_reconciled = false;

            $this->is_matched = false;
            
            return;
        }

        if ($this->currency->isZero($this->amount)) {
            $this->is_reconciled = true;

            $this->is_matched = true;

            return;
        }

        $residualField = ($this->currency_id === $this->company->currency_id)
            ? 'amount_residual'
            : 'amount_residual_currency';

        if (
            $this->journal->default_account_id
            && $liquidity->pluck('account_id')->contains($this->journal->default_account_id)
        ) {
            $this->is_matched = true;
        } else {
            $sumResidual = $liquidity->sum($residualField);

            $this->is_matched = $this->currency->isZero($sumResidual);
        }

        $reconcileLines = $counterpart
            ->merge($writeoff)
            ->filter(fn($line) => $line->account->reconcile);

        $sumReconcile = $reconcileLines->sum($residualField);

        $this->is_reconciled = $this->currency->isZero($sumReconcile);
    }

    public function computeOutstandingAccountId()
    {
        if ($this->outstanding_account_id) {
            return;
        }

        $this->outstanding_account_id = $this->paymentMethodLine->payment_account_id;
    }

    public function computeDestinationAccountId()
    {
        if ($this->destination_account_id) {
            return;
        }

        if ($this->partner_type === 'customer') {
            if ($this->partner_id) {
                $this->destination_account_id = $this->partner->property_account_receivable_id;
            } else {
                $this->destination_account_id = Account::where('account_type', AccountType::ASSET_RECEIVABLE)
                    ->where('deprecated', false)
                    ->first()
                    ?->id;
            }
        } elseif ($this->partner_type === 'supplier') {
            if ($this->partner_id) {
                $this->destination_account_id = $this->partner->property_account_payable_id;
            } else {
                $this->destination_account_id = Account::where('account_type', AccountType::LIABILITY_PAYABLE)
                    ->where('deprecated', false)
                    ->first()
                    ?->id;
            }
        }
    }

    public function computeAmountCompanyCurrencySigned()
    {
        if ($this->move_id) {
            [$liquidity] = $this->seekForLines();

            $this->amount_company_currency_signed = $liquidity->pluck('balance')->sum();
        } else {
            $this->amount_company_currency_signed = $this->currency->convert(
                fromAmount: $this->amount,
                toCurrency: $this->company->currency,
                company: $this->company,
                date: $this->date
            );
        }
    }

    public function generateJournalEntry($writeOffLineVals = null, $forceBalance = null, $lines = null)
    {
        if ($this->move_id || ! $this->outstanding_account_id) {
            return;
        }

        $move = Move::create([
            'move_type' => MoveType::ENTRY,
            'ref' => $this->memo,
            'date' => $this->date,
            'journal_id' => $this->journal_id,
            'company_id' => $this->company_id,
            'partner_id' => $this->partner_id,
            'currency_id' => $this->currency_id,
            'partner_bank_id' => $this->partner_bank_id,
            'origin_payment_id' => $this->id,
        ]);

        $lines = $lines ?: $this->prepareMoveLineDefaultVals($writeOffLineVals, $forceBalance);

        foreach ($lines as $lineVals) {
            $lineVals['move_id'] = $move->id;

            MoveLine::create($lineVals);
        }
        
        $this->move_id = $move->id;

        $this->state = 'in_process';
    }

    public function prepareMoveLineDefaultVals($writeOffLineVals = null, $forceBalance = null)
    {
        $writeOffLineVals = $writeOffLineVals ?: [];

        if (! $this->outstanding_account_id) {
            throw new \Exception(
                "You can't create a new payment without an outstanding payments/receipts account set either on the company or the " . 
                $this->paymentMethodLine->name . " payment method in the " . $this->journal->display_name . " journal."
            );
        }

        $writeOffLineValsList = $writeOffLineVals ?: [];

        $writeOffAmountCurrency = array_sum(array_column($writeOffLineValsList, 'amount_currency'));

        $writeOffBalance = array_sum(array_column($writeOffLineValsList, 'balance'));

        if ($this->payment_type == 'inbound') {
            $liquidityAmountCurrency = $this->amount;
        } elseif ($this->payment_type == 'outbound') {
            $liquidityAmountCurrency = -$this->amount;
        } else {
            $liquidityAmountCurrency = 0.0;
        }

        if (!$writeOffLineVals && $forceBalance !== null) {
            $sign = $liquidityAmountCurrency > 0 ? 1 : -1;

            $liquidityBalance = $sign * abs($forceBalance);
        } else {
            $liquidityBalance = $this->currency->convert(
                $liquidityAmountCurrency,
                $this->company->currency,
                $this->company,
                $this->date
            );
        }
        
        $counterpartAmountCurrency = -$liquidityAmountCurrency - $writeOffAmountCurrency;
        $counterpartBalance = -$liquidityBalance - $writeOffBalance;
        $currencyId = $this->currency_id;

        $counterpartLineName = $liquidityLineName = collect($this->getAmlDefaultDisplayNameList())->pluck('value')->implode('');

        $lineValsList = [
            [
                'name' => $liquidityLineName,
                'date_maturity' => $this->date,
                'amount_currency' => $liquidityAmountCurrency,
                'currency_id' => $currencyId,
                'debit' => $liquidityBalance > 0.0 ? $liquidityBalance : 0.0,
                'credit' => $liquidityBalance < 0.0 ? -$liquidityBalance : 0.0,
                'balance' => $liquidityBalance,
                'partner_id' => $this->partner_id,
                'account_id' => $this->outstanding_account_id,
            ], [
                'name' => $counterpartLineName,
                'date_maturity' => $this->date,
                'amount_currency' => $counterpartAmountCurrency,
                'currency_id' => $currencyId,
                'debit' => $counterpartBalance > 0.0 ? $counterpartBalance : 0.0,
                'credit' => $counterpartBalance < 0.0 ? -$counterpartBalance : 0.0,
                'balance' => $counterpartBalance,
                'partner_id' => $this->partner_id,
                'account_id' => $this->destination_account_id,
            ],
        ];
        
        return array_merge($lineValsList, $writeOffLineValsList);
    }

    public function synchronizeToMoves($changedFields)
    {
        if (!array_intersect($changedFields, $this->getTriggerFieldsToSynchronize())) {
            return;
        }

        foreach ($this as $pay) {
            [$liquidityLines, $counterpartLines, $writeoffLines] = $pay->seekForLines();
            
            $writeOffLineVals = [];
            if ($liquidityLines->isNotEmpty() && $counterpartLines->isNotEmpty() && $writeoffLines->isNotEmpty()) {
                $writeOffLineVals[] = [
                    'name' => $writeoffLines[0]->name,
                    'account_id' => $writeoffLines[0]->account_id,
                    'partner_id' => $writeoffLines[0]->partner_id,
                    'currency_id' => $writeoffLines[0]->currency_id,
                    'amount_currency' => $writeoffLines->sum('amount_currency'),
                    'balance' => $writeoffLines->sum('balance'),
                ];
            }
            
            $lineValsList = $pay->prepareMoveLineDefaultVals($writeOffLineVals);
            
            $lineIdsCommands = [
                $liquidityLines->isNotEmpty() 
                    ? ['id' => $liquidityLines->first()->id, ...$lineValsList[0]] 
                    : $lineValsList[0],
                $counterpartLines->isNotEmpty() 
                    ? ['id' => $counterpartLines->first()->id, ...$lineValsList[1]] 
                    : $lineValsList[1]
            ];
            
            foreach ($writeoffLines as $line) {
                $lineIdsCommands[] = ['delete' => $line->id];
            }
            
            for ($i = 2; $i < count($lineValsList); $i++) {
                $lineIdsCommands[] = $lineValsList[$i];
            }
            
            $pay->move->withContext(['skip_invoice_sync' => true])->update([
                'partner_id' => $pay->partner_id,
                'currency_id' => $pay->currency_id,
                'partner_bank_id' => $pay->partner_bank_id,
                'line_ids' => $lineIdsCommands,
            ]);
        }
    }

    public function seekForLines()
    {
        $lines = [collect(), collect(), collect()];

        $validAccountTypes = [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE];

        $validLiquidityAccounts = $this->getValidLiquidityAccounts();
        
        foreach ($this->move->lines as $line) {
            if ($validLiquidityAccounts->contains($line->account_id)) {
                $lines[0]->push($line);
            } elseif (
                in_array($line->account->account_type, $validAccountTypes)
                || $line->account_id == $line->company->transfer_account_id
            ) {
                $lines[1]->push($line);
            } else {
                $lines[2]->push($line);
            }
        }

        if ($lines[2]->count() == 1) {
            for ($i = 0; $i <= 1; $i++) {
                if ($lines[$i]->isEmpty()) {
                    $lines[$i] = $lines[2];

                    $lines[2] = collect();
                }
            }
        }

        return $lines;
    }

    public function getAmlDefaultDisplayNameList()
    {
        $label = $this->paymentMethodLine
            ? $this->paymentMethodLine->name
            : 'No Payment Method';

        
        if ($this->memo) {
            return [
                ['type' => 'label', 'value' => $label],
                ['type' => 'sep', 'value' => ': '],
                ['type' => 'memo', 'value' => $this->memo],
            ];
        }

        return [
            ['type' => 'label', 'value' => $label],
        ];
    }
}
