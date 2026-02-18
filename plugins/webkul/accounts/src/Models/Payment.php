<?php

namespace Webkul\Account\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Partner\Models\BankAccount;
use Webkul\Partner\Models\Partner;
use Webkul\Payment\Models\PaymentToken;
use Webkul\Payment\Models\PaymentTransaction;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class Payment extends Model
{
    use HasChatter, HasFactory, HasLogActivity;

    protected $table = 'accounts_account_payments';

    public function getModelTitle(): string
    {
        return __('accounts::models/payment.title');
    }

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
        'creator_id',
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

    protected $casts = [
        'date'         => 'date',
        'state'        => PaymentStatus::class,
        'payment_type' => PaymentType::class,
    ];

    public array $moveRelatedFields = [
        'partner_id',
        'currency_id',
        'partner_bank_id',
        'ref',
        'date',
    ];

    protected function getLogAttributeLabels(): array
    {
        return [
            'date'               => __('accounts::models/payment.log-attributes.date'),
            'payment_type'       => __('accounts::models/payment.log-attributes.payment-type'),
            'partner_type'       => __('accounts::models/payment.log-attributes.partner-type'),
            'memo'               => __('accounts::models/payment.log-attributes.memo'),
            'payment_reference'  => __('accounts::models/payment.log-attributes.payment-reference'),
            'amount'             => __('accounts::models/payment.log-attributes.amount'),
            'partner.name'       => __('accounts::models/payment.log-attributes.partner'),
            'partnerBank.name'   => __('accounts::models/payment.log-attributes.partner-bank'),
            'paymentMethod.name' => __('accounts::models/payment.log-attributes.payment-method'),
            'currency.name'      => __('accounts::models/payment.log-attributes.currency'),
        ];
    }

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
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

    public function getOutstandingAccount($paymentType)
    {
        $defaultAccountSettings = new DefaultAccountSettings;

        if ($this->payment_type == PaymentType::RECEIVE) {
            $accountId = $defaultAccountSettings->account_journal_payment_debit_account_id;
        } else {
            $accountId = $defaultAccountSettings->account_journal_payment_credit_account_id;
        }

        $account = Account::find($accountId);

        if (! $account) {
            $account = Account::find($defaultAccountSettings->transfer_account_id);
        }

        return $account;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->creator_id ??= Auth::id();
        });

        static::created(function ($move) {
            $move->computeName();

            $move->save();
        });

        static::saving(function ($payment) {
            $payment->computeState();

            $payment->computePartnerType();

            $payment->computeCreator();

            $payment->computeCompanyId();

            $payment->computePaymentMethodId();

            $payment->computeOutstandingAccountId();

            $payment->computeDestinationAccountId();

            $payment->computeAmountCompanyCurrencySigned();

            $payment->computeReconciliationStatus();
        });
    }

    public function computeName()
    {
        $this->name = $this->move?->name;

        if (! $this->name) {
            $prefix = '';

            if (
                $this->journal->refund_sequence
                && in_array($this->move_type, [MoveType::OUT_REFUND, MoveType::IN_REFUND])
            ) {
                $prefix .= 'R';
            }

            if ($this->journal->payment_sequence && $this->payment_method_line_id) {
                $prefix .= 'P';
            }

            $this->name = sprintf(
                '%s%s/%s',
                $prefix,
                $this->journal->code,
                $this->date->format('Y'),
            ).'/'.$this->id;
        }
    }

    public function computeCreator()
    {
        $this->creator_id = Auth::id();
    }

    public function computePartnerType()
    {
        if ($this->partner_type) {
            return;
        }

        $this->partner_type = $this->payment_type == PaymentType::RECEIVE ? 'customer' : 'supplier';
    }

    public function computeCompanyId()
    {
        if ($this->company_id) {
            return;
        }

        $this->company_id = $this->journal->company_id;
    }

    public function computeState()
    {
        if (! $this->state) {
            $this->state = PaymentStatus::DRAFT;
        }

        if (! $this->move) {
            return;
        }

        if ($this->state === PaymentStatus::IN_PROCESS && $this->outstanding_account_id) {
            [$liquidity] = $this->seekForLines();

            if (
                $this->move
                && $liquidity->isNotEmpty()
                && $this->move->currency->isZero($liquidity->sum('amount_residual'))
            ) {
                $this->state = PaymentStatus::PAID;

                return;
            }
        }

        if (
            $this->state === PaymentStatus::IN_PROCESS
            && $this->invoices()->exists()
            && $this->invoices->every(fn ($invoice) => $invoice->payment_state === PaymentStatus::PAID)
        ) {
            $this->state = PaymentStatus::PAID;
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
            $this->is_matched = $this->state === PaymentStatus::PAID;

            return;
        }

        if (! $this->currency_id || ! $this->id || ! $this->move_id) {
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

        $this->is_matched = $this->journal->default_account_id
            && $liquidity->pluck('account_id')->contains($this->journal->default_account_id)
            ? true
            : $this->currency->isZero($liquidity->sum($residualField));

        $reconcileLines = $counterpart
            ->merge($writeoff)
            ->filter(fn ($line) => $line->account->reconcile);

        $this->is_reconciled = $this->currency->isZero($reconcileLines->sum($residualField));
    }

    public function computePaymentMethodId()
    {
        $this->payment_method_id = $this->paymentMethodLine->payment_method_id;
    }

    public function computeOutstandingAccountId()
    {
        if ($this->outstanding_account_id) {
            return;
        }

        $this->outstanding_account_id = $this->paymentMethodLine->payment_account_id;

        if (! $this->outstanding_account_id) {
            $this->outstanding_account_id = $this->getOutstandingAccount($this->payment_type)?->id;
        }
    }

    public function computeDestinationAccountId()
    {
        if ($this->isDirty('destination_account_id') && $this->destination_account_id) {
            return;
        }

        $accountMapping = [
            'customer' => [
                'partner_property' => 'property_account_receivable_id',
                'account_type'     => AccountType::ASSET_RECEIVABLE,
            ],
            'supplier' => [
                'partner_property' => 'property_account_payable_id',
                'account_type'     => AccountType::LIABILITY_PAYABLE,
            ],
        ];

        if (! isset($accountMapping[$this->partner_type])) {
            return;
        }

        $mapping = $accountMapping[$this->partner_type];

        $this->destination_account_id = $this->partner?->{$mapping['partner_property']}
            ?? Account::where('account_type', $mapping['account_type'])
                ->where('deprecated', false)
                ->first()
                ?->id;
    }

    public function computeAmountCompanyCurrencySigned()
    {
        if ($this->move_id) {
            $this->amount_company_currency_signed = $this->seekForLines()[0]->sum('balance');
        } else {
            $this->amount_company_currency_signed = $this->currency->convert(
                fromAmount: $this->amount,
                toCurrency: $this->company->currency,
                company: $this->company,
                date: $this->date
            );
        }
    }

    public function computePaymentMethodLineId()
    {
        if (! $this->journal) {
            $this->payment_method_line_id = null;

            return;
        }

        $availablePaymentMethodLines = $this->journal->getAvailablePaymentMethodLines($this->payment_type);

        $inboundPaymentMethod = $this->partner?->property_inbound_payment_method_line_id;
        $outboundPaymentMethod = $this->partner?->property_outbound_payment_method_line;

        if ($this->payment_type == PaymentType::RECEIVE && $availablePaymentMethodLines->pluck('id')->contains($inboundPaymentMethod)) {
            $this->payment_method_line_id = $inboundPaymentMethod;
        } elseif ($this->payment_type == PaymentType::SEND && $availablePaymentMethodLines->pluck('id')->contains($outboundPaymentMethod)) {
            $this->payment_method_line_id = $outboundPaymentMethod;
        } elseif ($this->payment_method_line_id && $availablePaymentMethodLines->pluck('id')->contains($this->payment_method_line_id)) {
        } elseif ($availablePaymentMethodLines->isNotEmpty()) {
            $this->payment_method_line_id = $availablePaymentMethodLines->first()->id;
        } else {
            $this->payment_method_line_id = null;
        }
    }

    public function computeShowRequirePartnerBank()
    {
        $this->show_partner_bank_account = $this->journal->type == JournalType::CASH
            ? false
            : in_array($this->paymentMethodLine->code, (new Payment)->getMethodCodesUsingBankAccount());

        $this->require_partner_bank_account = in_array($this->paymentMethodLine->code, (new Payment)->getMethodCodesNeedingBankAccount());
    }

    public function generateJournalEntry($writeOffLineVals = null, $forceBalance = null, $lines = null)
    {
        if ($this->move_id || ! $this->outstanding_account_id) {
            return;
        }

        $move = Move::create([
            'move_type'         => MoveType::ENTRY,
            'ref'               => $this->memo,
            'date'              => $this->date,
            'journal_id'        => $this->journal_id,
            'company_id'        => $this->company_id,
            'partner_id'        => $this->partner_id,
            'currency_id'       => $this->currency_id,
            'partner_bank_id'   => $this->partner_bank_id,
            'origin_payment_id' => $this->id,
        ]);

        $lines = $lines ?: $this->prepareMoveLineDefaultVals($writeOffLineVals, $forceBalance);

        collect($lines)->each(fn ($lineVals) => MoveLine::create($lineVals + ['move_id' => $move->id]));

        AccountFacade::computeAccountMove($move);

        parent::updateQuietly([
            'move_id' => $move->id,
            'state'   => PaymentStatus::IN_PROCESS,
        ]);
    }

    public function prepareMoveLineDefaultVals($writeOffLineVals = null, $forceBalance = null)
    {
        if (! $this->outstanding_account_id) {
            throw new Exception(
                "You can't create a new payment without an outstanding payments/receipts account set either on the company or the ".
                    $this->paymentMethodLine->name.' payment method in the '.$this->journal->display_name.' journal.'
            );
        }

        $writeOffLineValsList = $writeOffLineVals ?: [];

        $writeOffAmountCurrency = collect($writeOffLineValsList)->sum('amount_currency');
        $writeOffBalance = collect($writeOffLineValsList)->sum('balance');

        $liquidityAmountCurrency = match ($this->payment_type) {
            PaymentType::RECEIVE => $this->amount,
            PaymentType::SEND    => -$this->amount,
            default              => 0.0,
        };

        $liquidityBalance = ! $writeOffLineVals && $forceBalance !== null
            ? ($liquidityAmountCurrency > 0 ? 1 : -1) * abs($forceBalance)
            : $this->currency->convert(
                $liquidityAmountCurrency,
                $this->company->currency,
                $this->company,
                $this->date
            );

        $counterpartAmountCurrency = -$liquidityAmountCurrency - $writeOffAmountCurrency;
        $counterpartBalance = -$liquidityBalance - $writeOffBalance;

        $lineName = $liquidityLineName = collect($this->getMoveLineDefaultDisplayNameList())->pluck('value')->implode('');

        $lineValsList = [
            [
                'name'            => $liquidityLineName,
                'date_maturity'   => $this->date,
                'amount_currency' => $liquidityAmountCurrency,
                'currency_id'     => $this->currency_id,
                'debit'           => $liquidityBalance > 0.0 ? $liquidityBalance : 0.0,
                'credit'          => $liquidityBalance < 0.0 ? -$liquidityBalance : 0.0,
                'balance'         => $liquidityBalance,
                'partner_id'      => $this->partner_id,
                'account_id'      => $this->outstanding_account_id,
            ],
            [
                'name'            => $lineName,
                'date_maturity'   => $this->date,
                'amount_currency' => $counterpartAmountCurrency,
                'currency_id'     => $this->currency_id,
                'debit'           => $counterpartBalance > 0.0 ? $counterpartBalance : 0.0,
                'credit'          => $counterpartBalance < 0.0 ? -$counterpartBalance : 0.0,
                'balance'         => $counterpartBalance,
                'partner_id'      => $this->partner_id,
                'account_id'      => $this->destination_account_id,
            ],
        ];

        return array_merge($lineValsList, $writeOffLineValsList);
    }

    public function synchronizeToMoves($changedFields)
    {
        if (! array_intersect($changedFields, $this->getTriggerFieldsToSynchronize())) {
            return;
        }

        foreach ($this as $pay) {
            [$liquidityLines, $counterpartLines, $writeoffLines] = $pay->seekForLines();

            $writeOffLineVals = [];

            if ($liquidityLines->isNotEmpty() && $counterpartLines->isNotEmpty() && $writeoffLines->isNotEmpty()) {
                $writeOffLineVals[] = [
                    'name'            => $writeoffLines[0]->name,
                    'account_id'      => $writeoffLines[0]->account_id,
                    'partner_id'      => $writeoffLines[0]->partner_id,
                    'currency_id'     => $writeoffLines[0]->currency_id,
                    'amount_currency' => $writeoffLines->sum('amount_currency'),
                    'balance'         => $writeoffLines->sum('balance'),
                ];
            }

            $lineValsList = $pay->prepareMoveLineDefaultVals($writeOffLineVals);

            $lineIdsCommands = [
                $liquidityLines->isNotEmpty()
                    ? ['id' => $liquidityLines->first()->id, ...$lineValsList[0]]
                    : $lineValsList[0],
                $counterpartLines->isNotEmpty()
                    ? ['id' => $counterpartLines->first()->id, ...$lineValsList[1]]
                    : $lineValsList[1],
            ];

            $lineIdsCommands = array_merge(
                $lineIdsCommands,
                $writeoffLines->map(fn ($line) => ['delete' => $line->id])->all(),
                array_slice($lineValsList, 2)
            );

            $pay->move->update([
                'partner_id'      => $pay->partner_id,
                'currency_id'     => $pay->currency_id,
                'partner_bank_id' => $pay->partner_bank_id,
                'line_ids'        => $lineIdsCommands,
            ]);
        }
    }

    public function seekForLines()
    {
        $lines = [collect(), collect(), collect()];

        $validAccountTypes = [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE];

        $validLiquidityAccounts = $this->getValidLiquidityAccounts();

        foreach ($this->move->lines as $line) {
            if ($validLiquidityAccounts->pluck('id')->contains($line->account_id)) {
                $lines[0]->push($line);
            } elseif (
                in_array($line->account->account_type, $validAccountTypes)
                || $line->account_id == (new DefaultAccountSettings)->transfer_account_id
            ) {
                $lines[1]->push($line);
            } else {
                $lines[2]->push($line);
            }
        }

        if ($lines[2]->count() == 1) {
            foreach ([0, 1] as $i) {
                if ($lines[$i]->isEmpty()) {
                    $lines[$i] = $lines[2];

                    $lines[2] = collect();
                }
            }
        }

        return $lines;
    }

    public function getMoveLineDefaultDisplayNameList()
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
