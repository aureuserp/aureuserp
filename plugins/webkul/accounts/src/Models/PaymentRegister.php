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

    public function computeAvailableJournalIds()
    {
        $availableJournals = collect();

        $batches = $this->prepareBatches();

        foreach ($batches as $batch) {
            $availableJournals = $availableJournals->merge($this->getBatchAvailableJournals($batch));
        }

        $this->available_journal_ids = $availableJournals->pluck('id')->unique()->toArray();
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

    public function prepareBatches()
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
            
            $merge = in_array($batchKey['partner_id'], $partnerUniqueInbound)
                && in_array($batchKey['partner_id'], $partnerUniqueOutbound);
            
            if ($merge) {
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
            
            if ($merge) {
                $partnerBanks = $banksPerPartner[$batchKey['partner_id']];

                $vals['partner_bank_id'] = $partnerBanks[$vals['payment_values']['payment_type']][0];

                $vals['lines'] = $lines;
            }
            
            $batchVals[] = $vals;
        }

        return $batchVals;
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
}
