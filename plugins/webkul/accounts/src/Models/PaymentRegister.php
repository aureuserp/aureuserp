<?php

namespace Webkul\Account\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Partner\Models\BankAccount;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class PaymentRegister extends Model
{
    use HasFactory;

    protected $table = 'accounts_payment_registers';

    protected $fillable = [
        'currency_id',
        'journal_id',
        'partner_bank_id',
        'custom_user_currency_id',
        'source_currency_id',
        'company_id',
        'partner_id',
        'payment_method_line_id',
        'writeoff_account_id',
        'creator_id',
        'communication',
        'installments_mode',
        'payment_type',
        'partner_type',
        'payment_difference_handling',
        'writeoff_label',
        'payment_date',
        'amount',
        'custom_user_amount',
        'source_amount',
        'source_amount_currency',
        'group_payment',
        'can_group_payments',
        'payment_token_id',
    ];

    protected $casts = [
        'payment_type' => PaymentType::class,
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function partnerBank()
    {
        return $this->belongsTo(BankAccount::class, 'partner_bank_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function customUserCurrency()
    {
        return $this->belongsTo(Currency::class, 'custom_user_currency_id');
    }

    public function sourceCurrency()
    {
        return $this->belongsTo(Currency::class, 'source_currency_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function paymentMethodLine()
    {
        return $this->belongsTo(PaymentMethodLine::class, 'payment_method_line_id');
    }

    public function writeoffAccount()
    {
        return $this->belongsTo(Account::class, 'writeoff_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines()
    {
        return $this->belongsToMany(MoveLine::class, 'accounts_account_payment_register_move_lines', 'payment_register_id', 'move_line_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paymentRegister) {
            $paymentRegister->creator_id ??= Auth::id();
        });

        static::retrieved(function ($paymentRegister) {
            $paymentRegister->computeBatches();

            $paymentRegister->computeIsSingleBatch();

            $paymentRegister->computeWriteoffIsExchangeAccount();

            $paymentRegister->computeAvailableJournalIds();

            $paymentRegister->computeShowRequirePartnerBank();
        });

        static::saving(function ($paymentRegister) {
            // $paymentRegister->computeBatches();

            $paymentRegister->computeGroupPayment();

            $paymentRegister->computePaymentDifferenceHandling();
        });
    }

    public function setLinesAttribute($lines)
    {
        $validLines = collect();

        foreach ($lines as $line) {
            if (! in_array($line->account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE])) {
                continue;
            }

            if ($line->currency?->isZero($line->amount_residual_currency)) {
                continue;
            }

            if ($line->company->currency?->isZero($line->amount_residual)) {
                continue;
            }

            $validLines->push($line);
        }

        $this->lines = $validLines;
    }

    public function computeIsSingleBatch()
    {
        $this->is_single_batch = count($this->batches) == 1;
    }

    public function computeGroupPayment()
    {
        $this->group_payment = $this->is_single_batch && $this->lines->count() > 1;
    }

    public function computeWriteoffIsExchangeAccount()
    {
        $this->writeoff_is_exchange_account = $this->is_single_batch
            && $this->currency_id != $this->source_currency_id
            && $this->writeoff_account_id
            && in_array($this->writeoff_account_id, [
                (new DefaultAccountSettings)->expense_currency_exchange_account_id,
                (new DefaultAccountSettings)->income_currency_exchange_account_id,
            ]);
    }

    public function computeInstallmentsMode()
    {
        if (! $this->journal_id || ! $this->currency_id) {
            return;
        }

        $totalAmountValues = $this->getTotalAmountsToPay($this->batches);

        if ($this->currency->compareAmounts($this->amount, $totalAmountValues['full_amount']) == 0) {
            $this->installments_mode = 'full';
        } elseif ($this->currency->compareAmounts($this->amount, $totalAmountValues['amount_by_default']) == 0) {
            $this->installments_mode = $totalAmountValues['installment_mode'];
        } else {
            $this->installments_mode = 'full';
        }
    }

    public function computeInstallmentsSwitchValues(): array
    {
        if (! $this->journal_id || ! $this->currency_id) {
            return [
                'installments_switch_amount' => 0.0,
                'installments_switch_html'   => null,
            ];
        }

        $totalAmountValues = $this->getTotalAmountsToPay($this->batches);
        $htmlLines = [];
        $switchAmount = 0.0;

        if ($this->installments_mode === 'full') {
            if (
                $this->currency->isZero($totalAmountValues['full_amount'] - $this->amount)
                && $this->currency->isZero($totalAmountValues['full_amount'] - $totalAmountValues['amount_by_default'])
            ) {
                $switchAmount = 0.0;
            } else {
                $switchAmount = $totalAmountValues['amount_by_default'];
                $htmlLines[] = __('This is the full amount.');
                $htmlLines[] = __('Consider paying in installments instead.');
            }
        } elseif ($this->installments_mode === 'overdue') {
            $switchAmount = $totalAmountValues['full_amount'];
            $htmlLines[] = __('This is the overdue amount.');
            $htmlLines[] = __('Consider paying the full amount.');
        } elseif ($this->installments_mode === 'before_date') {
            $switchAmount = $totalAmountValues['full_amount'];
            $nextPaymentDate = $this->getNextPaymentDateInContext();
            $htmlLines[] = __('Total for the installments before :date.', ['date' => $nextPaymentDate ?? now()->format('Y-m-d')]);
            $htmlLines[] = __('Consider paying the full amount.');
        } elseif ($this->installments_mode === 'next') {
            $switchAmount = $totalAmountValues['full_amount'];
            $htmlLines[] = __('This is the next unreconciled installment.');
            $htmlLines[] = __('Consider paying the full amount.');
        }

        if ($this->custom_user_amount) {
            $htmlText = null;
        } else {
            $htmlText = implode('<br/>', $htmlLines);
        }

        return [
            'installments_switch_amount' => $switchAmount,
            'installments_switch_html'   => $htmlText,
            'total_amounts'              => $totalAmountValues,
        ];
    }

    public function computeAvailableJournalIds()
    {
        $this->available_journal_ids = collect($this->batches)
            ->flatMap(fn ($batch) => $this->getBatchAvailableJournals($batch))
            ->pluck('id')
            ->unique()
            ->toArray();
    }

    public function computePaymentMethodLineId()
    {
        if ($this->journal) {
            $availablePaymentMethodLines = $this->journal->getAvailablePaymentMethodLines($this->payment_type);
        } else {
            $availablePaymentMethodLines = false;
        }

        if ($availablePaymentMethodLines && $availablePaymentMethodLines->contains($this->payment_method_line_id)) {
            return;
        }

        if ($availablePaymentMethodLines) {
            $movePaymentMethodLines = $this->lines->pluck('move')->pluck('paymentMethodLine')->unique()->filter();

            if ($movePaymentMethodLines->count() == 1 && $availablePaymentMethodLines->pluck('id')->contains($movePaymentMethodLines->first()->id)) {
                $this->paymentMethodLine = $movePaymentMethodLines->first();

                $this->payment_method_line_id = $movePaymentMethodLines->first()->id;
            } else {
                $this->paymentMethodLine = $availablePaymentMethodLines->first();

                $this->payment_method_line_id = $availablePaymentMethodLines->first()->id;
            }
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

    public function computePaymentDifferenceHandling()
    {
        if ($this->is_single_batch) {
            $this->payment_difference_handling = 'open';
        } else {
            $this->payment_difference_handling = false;
        }
    }

    public function computeFromLines()
    {
        $batch = $this->batches[0];

        $valuesFromBatch = $this->getValuesFromBatch($batch);

        if (count($this->batches) == 1) {
            $this->fill($valuesFromBatch);

            $this->is_single_batch = true;
        } else {
            $companyId = $this->batches
                ? $batch['lines']->sortBy(fn ($line) => count($line->company->parents->pluck('id')))->first()->company_id
                : null;

            $this->fill([
                'company_id'             => $companyId,
                'partner_id'             => null,
                'partner_type'           => null,
                'payment_type'           => $valuesFromBatch['payment_type'],
                'source_currency_id'     => null,
                'source_amount'          => null,
                'source_amount_currency' => null,
            ]);

            $this->is_single_batch = false;
        }
    }

    public function computeBatches()
    {
        $lines = $this->lines;

        if ($lines->pluck('company_id')->unique()->count() > 1) {
            throw new Exception("You can't create payments for entries belonging to different companies.");
        }

        if ($lines->isEmpty()) {
            throw new Exception("You can't open the register payment wizard without at least one receivable/payable line.");
        }

        $batches = [];
        $banksPerPartner = [];

        foreach ($lines as $line) {
            $batchKey = $this->getLineBatchKey($line);
            $batchKeyString = json_encode($batchKey);

            if (! isset($batches[$batchKeyString])) {
                $batches[$batchKeyString] = [
                    'lines'          => collect(),
                    'payment_values' => $batchKey,
                ];
            }

            $batches[$batchKeyString]['lines']->push($line);

            $partnerId = $batchKey['partner_id'];
            $direction = $line->balance > 0.0 ? 'inbound' : 'outbound';

            if (! isset($banksPerPartner[$partnerId])) {
                $banksPerPartner[$partnerId] = ['inbound' => [], 'outbound' => []];
            }

            if (! in_array($batchKey['partner_bank_id'], $banksPerPartner[$partnerId][$direction])) {
                $banksPerPartner[$partnerId][$direction][] = $batchKey['partner_bank_id'];
            }
        }

        $partnerUniqueInbound = collect($banksPerPartner)
            ->filter(fn ($banks) => count($banks['inbound']) == 1)
            ->keys()
            ->all();

        $partnerUniqueOutbound = collect($banksPerPartner)
            ->filter(fn ($banks) => count($banks['outbound']) == 1)
            ->keys()
            ->all();

        $batchVals = [];
        $seenKeys = [];
        $batchKeys = array_keys($batches);

        foreach ($batchKeys as $i => $key) {
            if (in_array($key, $seenKeys)) {
                continue;
            }

            $vals = $batches[$key];
            $lines = $vals['lines'];
            $batchKey = $vals['payment_values'];

            $shouldMerge = in_array($batchKey['partner_id'], $partnerUniqueInbound)
                && in_array($batchKey['partner_id'], $partnerUniqueOutbound);

            if ($shouldMerge) {
                for ($j = $i + 1; $j < count($batchKeys); $j++) {
                    $otherKey = $batchKeys[$j];

                    if (in_array($otherKey, $seenKeys)) {
                        continue;
                    }

                    $otherVals = $batches[$otherKey];

                    $allMatch = collect($vals['payment_values'])
                        ->filter(fn ($value, $key) => ! in_array($key, ['partner_bank_id', 'payment_type']))
                        ->every(fn ($value, $key) => $otherVals['payment_values'][$key] == $value);

                    if ($allMatch) {
                        $lines = $lines->merge($otherVals['lines']);
                        $seenKeys[] = $otherKey;
                    }
                }
            }

            $balance = $lines->sum('balance');
            $vals['payment_values']['payment_type'] = $balance > 0.0 ? PaymentType::RECEIVE : PaymentType::SEND;

            if ($shouldMerge) {
                $partnerBanks = $banksPerPartner[$batchKey['partner_id']];

                $vals['partner_bank_id'] = $partnerBanks[$vals['payment_values']['payment_type']][0];

                $vals['lines'] = $lines;
            }

            $batchVals[] = $vals;
        }

        $this->batches = $batchVals;
    }

    public function getBatchAvailableJournals($batchResult)
    {
        $paymentType = $batchResult['payment_values']['payment_type'];

        $companyId = $batchResult['lines']->first()->company_id;

        $paymentMethodRelation = $paymentType == 'inbound'
            ? 'inboundPaymentMethodLines'
            : 'outboundPaymentMethodLines';

        return Journal::where('company_id', $companyId)
            ->whereIn('type', [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])
            ->whereHas($paymentMethodRelation)
            ->get();
    }

    public function getLineBatchKey($line)
    {
        return [
            'partner_id'      => $line->partner_id,
            'account_id'      => $line->account_id,
            'currency_id'     => $line->currency_id,
            'partner_bank_id' => $line->move->isInvoice(true)
                ? $line->move->partnerBank?->id
                : null,
            'partner_type' => $line->account->account_type == AccountType::ASSET_RECEIVABLE ? 'customer' : 'supplier',
        ];
    }

    public function getBatchAccount($batch)
    {
        $partnerBankId = $batch['payment_values']['partner_bank_id'];

        $availablePartnerBanks = $this->getBatchAvailablePartnerBanks($batch, $this->journal);

        return $partnerBankId && $availablePartnerBanks->pluck('id')->contains($partnerBankId)
            ? BankAccount::find($partnerBankId)
            : $availablePartnerBanks->first();
    }

    public function getBatchAvailablePartnerBanks($batch, $journal)
    {
        $paymentValues = $batch['payment_values'];

        if ($paymentValues['payment_type'] == PaymentType::RECEIVE) {
            return collect($journal->bankAccount);
        }

        $company = $batch['lines']
            ->sortBy(fn ($line) => $line->company->parents->count())
            ->first()
            ->company;

        return $batch['lines']->first()->partner->bankAccounts
            ->filter(fn ($bankAccount) => ! $bankAccount->company_id || $bankAccount->company_id == $company->id);
    }

    public function getTotalAmountsToPay($batchResults)
    {
        $nextPaymentDate = $this->getNextPaymentDateInContext();

        $amountPerLineCommon = [];

        $amountPerLineFullAmount = [];

        $firstInstallmentMode = false;

        $allLines = collect($batchResults)
            ->pluck('lines')
            ->flatten()
            ->sortBy([
                fn ($line) => $line->move_id,
                fn ($line) => $line->date_maturity,
            ]);

        foreach ($allLines->groupBy('move_id') as $lines) {
            $installments = $lines->first()->move->getInstallmentsData(
                $lines,
                paymentDate: $this->payment_date,
                nextPaymentDate: $nextPaymentDate
            );

            $lastInstallmentMode = false;

            foreach ($installments as $installment) {
                $line = $installment['line'];

                if (
                    $line->display_type == DisplayType::PAYMENT_TERM
                    && in_array($installment['type'], ['overdue', 'next', 'before_date'])
                ) {
                    if ($installment['type'] == 'overdue') {
                        $amountPerLineCommon[] = $installment;
                    } elseif ($installment['type'] == 'before_date') {
                        $amountPerLineCommon[] = $installment;
                        $firstInstallmentMode = 'before_date';
                    } elseif ($installment['type'] == 'next') {
                        if (in_array($lastInstallmentMode, ['next', 'overdue', 'before_date'])) {
                            $amountPerLineFullAmount[] = $installment;
                        } elseif (! $lastInstallmentMode) {
                            $amountPerLineCommon[] = $installment;

                            $firstInstallmentMode = 'next';
                        }
                    }

                    $lastInstallmentMode = $installment['type'];

                    $firstInstallmentMode = $firstInstallmentMode ?: $lastInstallmentMode;

                    continue;
                }

                $amountPerLineCommon[] = $installment;
            }
        }

        $common = $this->convertToCurrentCurrency($amountPerLineCommon);

        $fullAmount = $this->convertToCurrentCurrency($amountPerLineFullAmount);

        $lines = collect($amountPerLineCommon)->pluck('line');

        return [
            'amount_by_default'          => abs($common),
            'full_amount'                => abs($common + $fullAmount),
            'amount_for_difference'      => abs($common),
            'full_amount_for_difference' => abs($common + $fullAmount),
            'installment_mode'           => $firstInstallmentMode,
            'lines'                      => $lines,
        ];
    }

    public function getNextPaymentDateInContext()
    {
        return $this->lines
            ->filter(fn ($line) => ! $line->reconciled && ! empty($line->payment_date))
            ->min('payment_date') ?? false;
    }

    public function convertToCurrentCurrency($installments)
    {
        $totalPerCurrency = collect($installments)
            ->groupBy('line.currency_id')
            ->map(fn ($group) => [
                'amount_residual'          => $group->sum('amount_residual'),
                'amount_residual_currency' => $group->sum('amount_residual_currency'),
            ]);

        return $totalPerCurrency->reduce(function ($totalAmount, $amounts, $currency) {
            $amountResidual = $amounts['amount_residual'];
            $amountResidualCurrency = $amounts['amount_residual_currency'];

            if ($currency == $this->currency->id) {
                return $totalAmount + $amountResidualCurrency;
            }

            if ($currency != $this->company->currency_id && $this->currency_id == $this->company->currency_id) {
                return $totalAmount + Currency::find($currency)
                    ->convert(
                        $amountResidualCurrency,
                        $this->company->currency,
                        $this->company,
                        $this->payment_date
                    );
            }

            return $totalAmount + Currency::find($this->company->currency_id)
                ->convert(
                    $amountResidual,
                    $this->currency,
                    $this->company,
                    $this->payment_date
                );
        }, 0.0);
    }

    public function getCommunication($lines)
    {
        if ($lines->pluck('move_id')->unique()->count() == 1) {
            $move = $lines->first()->move;

            $label = $move->payment_reference ?: ($move->ref ?: $move->name);
        } else {
            $label = sprintf(
                'BATCH/%s/%s',
                now()->format('Y'),
                $this->id
            );
        }

        return $label;
    }

    public function getValuesFromBatch($batch)
    {
        $paymentValues = $batch['payment_values'];

        $company = $batch['lines']->sortBy(fn ($line) => count($line->company->parents->pluck('id')))->first()->company;

        return [
            'company_id'             => $company->id,
            'partner_id'             => $paymentValues['partner_id'],
            'partner_type'           => $paymentValues['partner_type'],
            'payment_type'           => $paymentValues['payment_type'],
            'source_currency_id'     => $paymentValues['currency_id'],
            'source_amount'          => $sourceAmount = abs($batch['lines']->sum('amount_residual')),
            'source_amount_currency' => $paymentValues['currency_id'] == $company->currency_id
                ? $sourceAmount
                : abs($batch['lines']->sum('amount_residual_currency')),
        ];
    }
}
