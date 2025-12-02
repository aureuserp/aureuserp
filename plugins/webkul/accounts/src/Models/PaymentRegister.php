<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Partner\Models\BankAccount;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Account\Enums\JournalType;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Account\Enums\AccountType;

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

    public function creator()
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

        static::retrieved(function ($paymentRegister) {
            $paymentRegister->computeBatches();

            $paymentRegister->computeIsSingleBatch();

            $paymentRegister->computeWriteoffIsExchangeAccount();

            $paymentRegister->computeInstallmentsMode();

            $paymentRegister->computeAvailableJournalIds();

            $paymentRegister->computeShowRequirePartnerBank();
        });

        static::saving(function ($paymentRegister) {
            $paymentRegister->computeGroupPayment();

            $paymentRegister->computePaymentDifferenceHandling();

            $paymentRegister->computeFromLines();
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
        if ($this->is_single_batch && $this->lines->count() > 1) {
            $this->group_payment = true;
        } else {
            $this->group_payment = false;
        }
    }

    public function computeWriteoffIsExchangeAccount()
    {
        $this->writeoff_is_exchange_account = $this->is_single_batch
            && $this->currency_id != $this->source_currency_id
            && $this->writeoff_account_id
            && in_array($this->writeoff_account_id, [
                $this->company->expense_currency_exchange_account_id,
                $this->company->income_currency_exchange_account_id,
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

    public function computeAvailableJournalIds()
    {
        $availableJournals = collect();

        foreach ($this->batches as $batch) {
            $availableJournals = $availableJournals->merge($this->getBatchAvailableJournals($batch));
        }

        $this->available_journal_ids = $availableJournals->pluck('id')->unique()->toArray();
    }

    public function computeShowRequirePartnerBank()
    {
        if ($this->journal->type == JournalType::CASH) {
            $this->show_partner_bank_account = false;
        } else {
            $this->show_partner_bank_account = in_array($this->paymentMethodLine->code, (new Payment)->getMethodCodesUsingBankAccount());
        }

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
                ? $batch['lines']->sortBy(fn($line) => count($line->company->parents->pluck('id')))->first()->company_id
                : null;

            $this->fill([
                'company_id' => $companyId,
                'partner_id' => null,
                'partner_type' => null,
                'payment_type' => $valuesFromBatch['payment_type'],
                'source_currency_id' => null,
                'source_amount' => null,
                'source_amount_currency' => null,
            ]);

            $this->is_single_batch = false;
        }
    }

    public function computeBatches()
    {
        $lines = $this->lines;

        if ($lines->pluck('company_id')->unique()->count() > 1) {
            throw new \Exception("You can't create payments for entries belonging to different companies.");
        }

        if ($lines->isEmpty()) {
            throw new \Exception("You can't open the register payment wizard without at least one receivable/payable line.");
        }

        $batches = [];

        $banksPerPartner = [];
        
        foreach ($lines as $line) {
            $batchKey = $this->getLineBatchKey($line);

            $batchKeyString = json_encode($batchKey);
            
            if (! isset($batches[$batchKeyString])) {
                $batches[$batchKeyString] = [
                    'lines' => collect(),
                    'payment_values' => $batchKey,
                ];
            }
            
            $batches[$batchKeyString]['lines']->push($line);
            
            $partnerId = $batchKey['partner_id'];

            if (!isset($banksPerPartner[$partnerId])) {
                $banksPerPartner[$partnerId] = ['inbound' => [], 'outbound' => []];
            }
            
            $direction = $line->balance > 0.0 ? 'inbound' : 'outbound';

            if (!in_array($batchKey['partner_bank_id'], $banksPerPartner[$partnerId][$direction])) {
                $banksPerPartner[$partnerId][$direction][] = $batchKey['partner_bank_id'];
            }
        }

        $partnerUniqueInbound = [];

        $partnerUniqueOutbound = [];
        
        foreach ($banksPerPartner as $p => $b) {
            if (count($b['inbound']) == 1) {
                $partnerUniqueInbound[] = $p;
            }

            if (count($b['outbound']) == 1) {
                $partnerUniqueOutbound[] = $p;
            }
        }

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

                    $allMatch = true;
                    
                    foreach ($vals['payment_values'] as $k => $v) {
                        if (!in_array($k, ['partner_bank_id', 'payment_type'])) {
                            if ($otherVals['payment_values'][$k] != $v) {
                                $allMatch = false;

                                break;
                            }
                        }
                    }
                    
                    if ($allMatch) {
                        $lines = $lines->merge($otherVals['lines']);

                        $seenKeys[] = $otherKey;
                    }
                }
            }
            
            $balance = $lines->sum('balance');

            $vals['payment_values']['payment_type'] = $balance > 0.0 ? 'inbound' : 'outbound';
            
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
        
        $journals = Journal::where('company_id', $companyId)
            ->whereIn('type', [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])
            ->get();
        
        if ($paymentType == 'inbound') {
            return $journals->filter(function($journal) {
                return $journal->inboundPaymentMethodLines->isNotEmpty();
            });
        } else {
            return $journals->filter(function($journal) {
                return $journal->outboundPaymentMethodLines->isNotEmpty();
            });
        }
    }

    public function getLineBatchKey($line)
    {
        $move = $line->move;

        $partnerBankAccount = new BankAccount;

        if ($move->isInvoice(true)) {
            $partnerBankAccount = $move->partnerBank;
        }

        return [
            'partner_id' => $line->partner_id,
            'account_id' => $line->account_id,
            'currency_id' => $line->currency_id,
            'partner_bank_id' => $partnerBankAccount?->id,
            'partner_type' => $line->account->account_type == AccountType::ASSET_RECEIVABLE ? 'customer' : 'supplier',
        ];
    }

    public function getBatchAccount($batch)
    {
        $partnerBankId = $batch['payment_values']['partner_bank_id'];

        $availablePartnerBanks = $this->getBatchAvailablePartnerBanks($batch, $this->journal);

        if ($partnerBankId && $availablePartnerBanks->pluck('id')->contains($partnerBankId)) {
            return BankAccount::find($partnerBankId);
        } else {
            return $availablePartnerBanks->first();
        }
    }

    public function getBatchAvailablePartnerBanks($batch, $journal)
    {
        $paymentValues = $batch['payment_values'];

        if ($paymentValues['payment_type'] == 'inbound') {
            return collect($journal->bankAccount);
        } else {
            $company = $batch['lines']->sortBy(fn($line) => count($line->company->parents->pluck('id')))->first()->company;

            return $batch['lines']->first()->partner->bankAccounts
                ->filter(function ($bankAccount) use ($company) {
                    return ! $bankAccount->company_id || $bankAccount->company_id == $company->id;
                });
        }
    }

    public function getTotalAmountsToPay($batchResults)
    {
        $nextPaymentDate = $this->getNextPaymentDateInContext();

        $amountPerLineCommon = [];

        $amountPerLineFullAmount = [];

        $firstInstallmentMode = false;

        $allLines = collect();
        
        foreach ($batchResults as $batchResult) {
            $allLines = $allLines->merge($batchResult['lines']);
        }
        
        $allLines = $allLines->sortBy(function($line) {
            return [$line->move_id, $line->date_maturity];
        });
        
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
                    $line->display_type == 'payment_term'
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
                        } elseif (!$lastInstallmentMode) {
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

        $lines = collect();

        foreach ($amountPerLineCommon as $value) {
            $lines->push($value['line']);
        }

        return [
            'amount_by_default' => abs($common),
            'full_amount' => abs($common + $fullAmount),
            'amount_for_difference' => abs($common),
            'full_amount_for_difference' => abs($common + $fullAmount),
            'installment_mode' => $firstInstallmentMode,
            'lines' => $lines,
        ];
    }

    //TODO: need to pass context
    public function getNextPaymentDateInContext()
    {
        return $this->payment_date;
    }

    public function convertToCurrentCurrency($installments)
    {
        $totalPerCurrency = [];
        
        foreach ($installments as $installment) {
            $line = $installment['line'];

            if (!isset($totalPerCurrency[$line->currency_id])) {
                $totalPerCurrency[$line->currency_id] = [
                    'amount_residual' => 0.0,
                    'amount_residual_currency' => 0.0,
                ];
            }
            
            $totalPerCurrency[$line->currency_id]['amount_residual'] += $installment['amount_residual'];

            $totalPerCurrency[$line->currency_id]['amount_residual_currency'] += $installment['amount_residual_currency'];
        }

        $totalAmount = 0.0;

        foreach ($totalPerCurrency as $currency => $amounts) {
            $amountResidual = $amounts['amount_residual'];

            $amountResidualCurrency = $amounts['amount_residual_currency'];
            
            if ($currency == $this->currency->id) {
                $totalAmount += $amountResidualCurrency;
            } elseif ($currency != $this->company->currency_id && $this->currency_id == $this->company->currency_id) {
                $totalAmount += Currency::find($currency->id)
                    ->convert(
                        $amountResidualCurrency, 
                        $this->company->currency, 
                        $this->company, 
                        $this->payment_date
                    );
            } elseif ($currency == $this->company->currency_id && $this->currency_id != $this->company->currency_id) {
                $totalAmount += Currency::find($this->company->currency_id)
                    ->convert(
                        $amountResidual, 
                        $this->currency, 
                        $this->company, 
                        $this->payment_date
                    );
            } else {
                $totalAmount += Currency::find($this->company->currency_id)
                    ->convert(
                        $amountResidual, 
                        $this->currency, 
                        $this->company, 
                        $this->payment_date
                    );
            }
        }
        
        return $totalAmount;
    }

    public function getCommunication($lines)
    {
        if ($lines->pluck('move_id')->unique()->count() == 1) {
            $move = $lines->first()->move;
            
            $label = $move->payment_reference ?: ($move->ref ?: $move->name);
        } else {
            //TODO: need to calculate the Batch sequence number
            $label = 'Batch';
        }

        return $label;
    }

    public function getValuesFromBatch($batch)
    {
        $paymentValues = $batch['payment_values'];

        $lines = $batch['lines'];

        $company = $lines->sortBy(fn($line) => count($line->company->parents->pluck('id')))->first()->company;

        $sourceAmount = abs($lines->sum('amount_residual'));

        if ($paymentValues['currency_id'] == $company->currency_id) {
            $sourceAmountCurrency = $sourceAmount;
        } else {
            $sourceAmountCurrency = abs($lines->sum('amount_residual_currency'));
        }

        return [
            'company_id' => $company->id,
            'partner_id' => $paymentValues['partner_id'],
            'partner_type' => $paymentValues['partner_type'],
            'payment_type' => $paymentValues['payment_type'],
            'source_currency_id' => $paymentValues['currency_id'],
            'source_amount' => $sourceAmount,
            'source_amount_currency' => $sourceAmountCurrency,
        ];
    }
}
