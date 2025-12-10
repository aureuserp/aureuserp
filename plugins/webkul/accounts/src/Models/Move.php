<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\AccountType;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Partner\Models\BankAccount;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Models\UTMMedium;
use Webkul\Support\Models\UTMSource;

class Move extends Model implements Sortable
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, SortableTrait;

    protected $table = 'accounts_account_moves';

    protected $fillable = [
        'sort',
        'journal_id',
        'company_id',
        'campaign_id',
        'tax_cash_basis_origin_move_id',
        'auto_post_origin_id',
        'origin_payment_id',
        'secure_sequence_number',
        'invoice_payment_term_id',
        'partner_id',
        'commercial_partner_id',
        'partner_shipping_id',
        'partner_bank_id',
        'fiscal_position_id',
        'currency_id',
        'reversed_entry_id',
        'invoice_user_id',
        'invoice_incoterm_id',
        'invoice_cash_rounding_id',
        'preferred_payment_method_line_id',
        'creator_id',
        'sequence_prefix',
        'access_token',
        'name',
        'reference',
        'state',
        'move_type',
        'auto_post',
        'inalterable_hash',
        'payment_reference',
        'qr_code_method',
        'payment_state',
        'invoice_source_email',
        'invoice_partner_display_name',
        'invoice_origin',
        'incoterm_location',
        'date',
        'auto_post_until',
        'invoice_date',
        'invoice_date_due',
        'delivery_date',
        'sending_data',
        'narration',
        'invoice_currency_rate',
        'amount_untaxed',
        'amount_tax',
        'amount_total',
        'amount_residual',
        'amount_untaxed_signed',
        'amount_untaxed_in_currency_signed',
        'amount_tax_signed',
        'amount_total_signed',
        'amount_total_in_currency_signed',
        'amount_residual_signed',
        'quick_edit_total_amount',
        'is_storno',
        'always_tax_exigible',
        'checked',
        'posted_before',
        'made_sequence_gap',
        'is_manually_modified',
        'is_move_sent',
        'source_id',
        'medium_id',
    ];

    protected array $logAttributes = [
        'medium.name'                       => 'Medium',
        'source.name'                       => 'UTM Source',
        'partner.name'                      => 'Customer',
        'commercialPartner.name'            => 'Commercial Partner',
        'partnerShipping.name'              => 'Shipping Address',
        'partnerBank.name'                  => 'Bank Account',
        'fiscalPosition.name'               => 'Fiscal Position',
        'currency.name'                     => 'Currency',
        'reversedEntry.name'                => 'Reversed Entry',
        'invoiceUser.name'                  => 'Invoice User',
        'invoiceIncoterm.name'              => 'Invoice Incoterm',
        'invoiceCashRounding.name'          => 'Invoice Cash Rounding',
        'createdBy.name'                    => 'Created By',
        'name'                              => 'Invoice Reference',
        'state'                             => 'Invoice Status',
        'reference'                         => 'Reference',
        'invoiceSourceEmail'                => 'Source Email',
        'invoicePartnerDisplayName'         => 'Partner Display Name',
        'invoiceOrigin'                     => 'Invoice Origin',
        'incotermLocation'                  => 'Incoterm Location',
        'date'                              => 'Invoice Date',
        'invoice_date'                      => 'Invoice Date',
        'invoice_date_due'                  => 'Due Date',
        'delivery_date'                     => 'Delivery Date',
        'narration'                         => 'Notes',
        'amount_untaxed'                    => 'Subtotal',
        'amount_tax'                        => 'Tax',
        'amount_total'                      => 'Total',
        'amount_residual'                   => 'Residual',
        'amount_untaxed_signed'             => 'Subtotal (Signed)',
        'amount_untaxed_in_currency_signed' => 'Subtotal (In Currency) (Signed)',
        'amount_tax_signed'                 => 'Tax (Signed)',
        'amount_total_signed'               => 'Total (Signed)',
        'amount_total_in_currency_signed'   => 'Total (In Currency) (Signed)',
        'amount_residual_signed'            => 'Residual (Signed)',
        'quick_edit_total_amount'           => 'Quick Edit Total Amount',
        'is_storno'                         => 'Is Storno',
        'always_tax_exigible'               => 'Always Tax Exigible',
        'checked'                           => 'Checked',
        'posted_before'                     => 'Posted Before',
        'made_sequence_gap'                 => 'Made Sequence Gap',
        'is_manually_modified'              => 'Is Manually Modified',
        'is_move_sent'                      => 'Is Move Sent',
    ];

    protected $casts = [
        'invoice_date'     => 'date',
        'date'             => 'date',
        'invoice_date_due' => 'datetime',
        'state'            => MoveState::class,
        'payment_state'    => PaymentState::class,
        'move_type'        => MoveType::class,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function campaign()
    {
        return $this->belongsTo(UtmCampaign::class, 'campaign_id');
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function taxCashBasisOriginMove()
    {
        return $this->belongsTo(Move::class, 'tax_cash_basis_origin_move_id');
    }

    public function autoPostOrigin()
    {
        return $this->belongsTo(Move::class, 'auto_post_origin_id');
    }

    public function invoicePaymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class, 'invoice_payment_term_id')->withTrashed();
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function commercialPartner()
    {
        return $this->belongsTo(Partner::class, 'commercial_partner_id');
    }

    public function partnerShipping()
    {
        return $this->belongsTo(Partner::class, 'partner_shipping_id');
    }

    public function partnerBank()
    {
        return $this->belongsTo(BankAccount::class, 'partner_bank_id')->withTrashed();
    }

    public function fiscalPosition()
    {
        return $this->belongsTo(FiscalPosition::class, 'fiscal_position_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function reversedEntry()
    {
        return $this->belongsTo(self::class, 'reversed_entry_id');
    }

    public function invoiceUser()
    {
        return $this->belongsTo(User::class, 'invoice_user_id');
    }

    public function invoiceIncoterm()
    {
        return $this->belongsTo(Incoterm::class, 'invoice_incoterm_id');
    }

    public function invoiceCashRounding()
    {
        return $this->belongsTo(CashRounding::class, 'invoice_cash_rounding_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function source()
    {
        return $this->belongsTo(UTMSource::class, 'source_id');
    }

    public function medium()
    {
        return $this->belongsTo(UTMMedium::class, 'medium_id');
    }

    public function paymentMethodLine()
    {
        return $this->belongsTo(PaymentMethodLine::class, 'preferred_payment_method_line_id');
    }

    public function getResourceUrl(?string $page = 'edit'): ?string
    {
        if (! $this->id || ! $this->move_type) {
            return null;
        }

        $resourceClass = match ($this->move_type) {
            \Webkul\Account\Enums\MoveType::OUT_INVOICE => \Webkul\Account\Filament\Resources\InvoiceResource::class,
            \Webkul\Account\Enums\MoveType::OUT_REFUND  => \Webkul\Account\Filament\Resources\CreditNoteResource::class,
            \Webkul\Account\Enums\MoveType::IN_INVOICE  => \Webkul\Account\Filament\Resources\BillResource::class,
            \Webkul\Account\Enums\MoveType::IN_REFUND   => \Webkul\Account\Filament\Resources\RefundResource::class,
            default                                     => null,
        };

        return $resourceClass
            ? $resourceClass::getUrl($page, ['record' => $this->id])
            : null;
    }

    public function getTotalDiscountAttribute()
    {
        return $this->lines()
            ->where('display_type', 'product')
            ->sum('discount');
    }

    public function isInbound($includeReceipts = true)
    {
        return in_array($this->move_type, $this->getInboundTypes($includeReceipts));
    }

    public function getInboundTypes($includeReceipts = true): array
    {
        $types = [MoveType::OUT_INVOICE, MoveType::IN_REFUND];

        if ($includeReceipts) {
            $types[] = MoveType::OUT_RECEIPT;
        }

        return $types;
    }

    public function isOutbound($includeReceipts = true)
    {
        return in_array($this->move_type, $this->getOutboundTypes($includeReceipts));
    }

    public function getOutboundTypes($includeReceipts = true): array
    {
        $types = [MoveType::IN_INVOICE, MoveType::OUT_REFUND];

        if ($includeReceipts) {
            $types[] = MoveType::IN_RECEIPT;
        }

        return $types;
    }

    public function getDirectionSignAttribute()
    {
        if ($this->isEntry() || $this->isOutbound()) {
            return 1;
        }

        return -1;
    }

    public function lines()
    {
        return $this->hasMany(MoveLine::class, 'move_id');
    }

    public function invoiceLines()
    {
        return $this->hasMany(MoveLine::class, 'move_id')
            ->where('display_type', DisplayType::PRODUCT);
    }

    public function taxLines()
    {
        return $this->hasMany(MoveLine::class, 'move_id')
            ->where('display_type', DisplayType::TAX);
    }

    public function paymentTermLines()
    {
        return $this->hasMany(MoveLine::class, 'move_id')
            ->where('display_type', DisplayType::PAYMENT_TERM);
    }

    public function roundingLines()
    {
        return $this->hasMany(MoveLine::class, 'move_id')
            ->where('display_type', DisplayType::ROUNDING);
    }

    public function matchedPayments()
    {
        return $this->belongsToMany(Tax::class, 'accounts_accounts_move_payment', 'invoice_id', 'payment_id');
    }

    public function isInvoice($includeReceipts = false)
    {
        return $this->isSaleDocument($includeReceipts) || $this->isPurchaseDocument($includeReceipts);
    }

    public function isEntry()
    {
        return $this->move_type === MoveType::ENTRY;
    }

    public function getSaleTypes($includeReceipts = false)
    {
        return $includeReceipts
            ? [MoveType::OUT_INVOICE, MoveType::OUT_REFUND, MoveType::OUT_RECEIPT]
            : [MoveType::OUT_INVOICE, MoveType::OUT_REFUND];
    }

    public function isSaleDocument($includeReceipts = false)
    {
        return in_array($this->move_type, $this->getSaleTypes($includeReceipts));
    }

    public function isPurchaseDocument($includeReceipts = false)
    {
        return in_array($this->move_type, $includeReceipts ? [
            MoveType::IN_INVOICE,
            MoveType::IN_REFUND,
            MoveType::IN_RECEIPT,
        ] : [MoveType::IN_INVOICE, MoveType::IN_REFUND]);
    }

    public function getValidJournalTypes()
    {
        if ($this->isSaleDocument(true)) {
            return [JournalType::SALE];
        } elseif ($this->isPurchaseDocument(true)) {
            return [JournalType::PURCHASE];
        } elseif ($this->origin_payment_id || $this->statement_line_id) {
            return [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD];
        } else {
            return [JournalType::GENERAL];
        }
    }

    public function getInvoiceInPaymentState()
    {
        return PaymentState::PAID;
    }

    /**
     * Bootstrap any application services.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($move) {
            $move->creator_id = auth()->id();
        });

        static::created(function ($move) {
            $move->computeName();

            $move->save();
        });

        static::saving(function ($move) {
            $move->computePartnerDisplayInfo();

            $move->computePartnerShippingId();

            $move->computeCommercialPartnerId();

            $move->computeJournalId();

            $move->computeInvoiceCurrencyRate();

            $move->computeInvoiceDateDue();

            $move->computePaymentState();
        });
    }

    public function computeName()
    {
        $prefix = '';

        if (
            $this->journal->refund_sequence 
            && in_array($this->move_type, [MoveType::OUT_REFUND, MoveType::IN_REFUND])
        ) {
            $prefix .= 'R';
        }

        if ($this->journal->payment_sequence && $this->origin_payment_id) {
            $prefix .= 'P';
        }

        $this->sequence_prefix = sprintf(
            '%s%s/%s',
            $prefix,
            $this->journal->code,
            $this->date->format('Y'),
        );

        $this->name = $this->sequence_prefix.'/'.$this->id;
    }

    public function computePartnerDisplayInfo()
    {
        $vendorDisplayName = $this->partner?->name;

        if (! $vendorDisplayName) {
            if ($this->invoice_source_email) {
                $vendorDisplayName = "@From: {$this->invoice_source_email}";
            } else {
                $vendorDisplayName = "#Created by: {$this->createdBy->name}";
            }
        }

        $this->invoice_partner_display_name = $vendorDisplayName;
    }

    public function computeInvoiceCurrencyRate()
    {
        $this->invoice_currency_rate = 1;
    }

    public function computePartnerShippingId()
    {
        $this->partner_shipping_id = $this->partner_id;
    }

    public function computeCommercialPartnerId()
    {
        $this->commercial_partner_id = $this->partner_id;
    }

    public function computeInvoiceDateDue()
    {
        $dateMaturity = now();

        if ($this->invoicePaymentTerm) {
            $dueTerm = $this->invoicePaymentTerm->dueTerm;

            if ($dueTerm) {
                switch ($dueTerm->delay_type) {
                    case DelayType::DAYS_AFTER->value:
                        $dateMaturity = $dateMaturity->addDays((int) $dueTerm->nb_days);

                        break;

                    case DelayType::DAYS_AFTER_END_OF_MONTH->value:
                        $dateMaturity = $dateMaturity->endOfMonth()->addDays((int) $dueTerm->nb_days);

                        break;

                    case DelayType::DAYS_AFTER_END_OF_NEXT_MONTH->value:
                        $dateMaturity = $dateMaturity->addMonth()->endOfMonth()->addDays((int) $dueTerm->days_next_month);

                        break;

                    case DelayType::DAYS_END_OF_MONTH_NO_THE->value:
                        $dateMaturity = $dateMaturity->endOfMonth();

                        break;
                }
            }
        }

        $this->invoice_date_due = $dateMaturity;
    }

    public function computeJournalId()
    {
        if (! in_array($this->journal?->type, $this->getValidJournalTypes())) {
            $this->journal_id = $this->searchDefaultJournal($this)?->id;
        }
    }

    public function searchDefaultJournal()
    {
        $validJournalTypes = $this->getValidJournalTypes();

        return Journal::where('company_id', $this->company_id)
            ->whereIn('type', $validJournalTypes)
            ->first();
    }

    public function computePaymentState()
    {
        $debitResults = PartialReconcile::select(
            'source_line.id as source_line_id',
            'source_line.move_id as source_move_id',
            'account.account_type as source_line_account_type',
            DB::raw('JSON_ARRAYAGG(opposite_move.move_type) as opposite_move_types'),
            DB::raw("
                CASE 
                    WHEN SUM(opposite_move.origin_payment_id IS NOT NULL) = 0 
                        THEN TRUE
                    ELSE MIN(COALESCE(payment.is_matched, 0))
                END AS all_payments_matched
            "),
            DB::raw('MAX(payment.id IS NOT NULL) as has_payment'),
            DB::raw('MAX(opposite_move.statement_line_id IS NOT NULL) as has_statement_line')
        )
            ->from('accounts_partial_reconciles as partial_reconciles')
            ->join('accounts_account_move_lines as source_line', 'source_line.id', '=', 'partial_reconciles.debit_move_id')
            ->join('accounts_accounts as account', 'account.id', '=', 'source_line.account_id')
            ->join('accounts_account_move_lines as opposite_line', 'opposite_line.id', '=', 'partial_reconciles.credit_move_id')
            ->join('accounts_account_moves as opposite_move', 'opposite_move.id', '=', 'opposite_line.move_id')
            ->leftJoin('accounts_account_payments as payment', 'payment.id', '=', 'opposite_move.origin_payment_id')
            ->where('source_line.move_id', $this->id)
            ->whereColumn('opposite_line.move_id', '!=', 'source_line.move_id')
            ->groupBy('source_line.id', 'source_line.move_id', 'account.account_type')
            ->get();

        $creditResults = PartialReconcile::select(
            'source_line.id as source_line_id',
            'source_line.move_id as source_move_id',
            'account.account_type as source_line_account_type',
            DB::raw('JSON_ARRAYAGG(opposite_move.move_type) as opposite_move_types'),
            DB::raw("
                CASE 
                    WHEN SUM(opposite_move.origin_payment_id IS NOT NULL) = 0 
                        THEN TRUE
                    ELSE MIN(COALESCE(payment.is_matched, 0))
                END AS all_payments_matched
            "),
            DB::raw('MAX(payment.id IS NOT NULL) as has_payment'),
            DB::raw('MAX(opposite_move.statement_line_id IS NOT NULL) as has_statement_line')
        )
            ->from('accounts_partial_reconciles as partial_reconciles')
            ->join('accounts_account_move_lines as source_line', 'source_line.id', '=', 'partial_reconciles.credit_move_id')
            ->join('accounts_accounts as account', 'account.id', '=', 'source_line.account_id')
            ->join('accounts_account_move_lines as opposite_line', 'opposite_line.id', '=', 'partial_reconciles.debit_move_id')
            ->join('accounts_account_moves as opposite_move', 'opposite_move.id', '=', 'opposite_line.move_id')
            ->leftJoin('accounts_account_payments as payment', 'payment.id', '=', 'opposite_move.origin_payment_id')
            ->where('source_line.move_id', $this->id)
            ->whereColumn('opposite_line.move_id', '!=', 'source_line.move_id')
            ->groupBy('source_line.id', 'source_line.move_id', 'account.account_type')
            ->get();

        $allResults = $debitResults->merge($creditResults);

        $paymentData = [];
        
        foreach ($allResults as $row) {
            $oppositeMoveTypes = $row->opposite_move_types;

            if (is_string($oppositeMoveTypes)) {
                $oppositeMoveTypes = str_replace(['{', '}'], '', $oppositeMoveTypes);

                $oppositeMoveTypes = $oppositeMoveTypes ? explode(',', $oppositeMoveTypes) : [];
            }
            
            $paymentData[] = [
                'source_line_id' => $row->source_line_id,
                'source_move_id' => $row->source_move_id,
                'source_line_account_type' => $row->source_line_account_type,
                'opposite_move_types' => $oppositeMoveTypes,
                'all_payments_matched' => $row->all_payments_matched === true,
                'has_payment' => $row->has_payment === true,
                'has_statement_line' => $row->has_statement_line === true,
            ];
        }

        $currencies = $this->lines->pluck('currency_id')->unique();

        $currency = $currencies->count() === 1 
            ? Currency::find($currencies->first()) 
            : $this->company->currency_id;
        
        $reconciliationVals = $paymentData;

        $paymentStateNeeded = $this->isInvoice(true);

        if ($paymentStateNeeded) {
            $reconciliationVals = array_filter($reconciliationVals, function ($row) {
                return in_array($row['source_line_account_type'], ['asset_receivable', 'liability_payable']);
            });
        }

        $newPaymentState = $this->payment_state !== PaymentState::BLOCKED ? PaymentState::NOT_PAID : PaymentState::BLOCKED;
        
        if ($this->state === MoveState::POSTED && $paymentStateNeeded) {
            if ($currency->isZero($this->amount_residual)) {
                $hasPaymentOrStatementLine = false;

                foreach ($reconciliationVals as $row) {
                    if ($row['has_payment'] || $row['has_statement_line']) {
                        $hasPaymentOrStatementLine = true;

                        break;
                    }
                }
                
                if ($hasPaymentOrStatementLine) {
                    $allPaymentsMatched = true;

                    foreach ($reconciliationVals as $row) {
                        if (!$row['all_payments_matched']) {
                            $allPaymentsMatched = false;

                            break;
                        }
                    }
                    
                    if ($allPaymentsMatched) {
                        $newPaymentState = PaymentState::PAID;
                    } else {
                        $newPaymentState = $this->getInvoiceInPaymentState();
                    }
                } else {
                    $newPaymentState = PaymentState::PAID;

                    $reverseMoveTypes = [];

                    foreach ($reconciliationVals as $row) {
                        foreach ($row['opposite_move_types'] as $moveType) {
                            $reverseMoveTypes[$moveType] = true;
                        }
                    }

                    $reverseMoveTypes = array_keys($reverseMoveTypes);

                    sort($reverseMoveTypes);

                    $inReverse = in_array($this->move_type, [MoveType::IN_INVOICE, MoveType::IN_RECEIPT])
                        && (
                            $reverseMoveTypes === [MoveType::IN_REFUND] 
                            || (
                                count($reverseMoveTypes) === 2
                                && in_array(MoveType::IN_REFUND, $reverseMoveTypes)
                                && in_array(MoveType::ENTRY, $reverseMoveTypes)
                            )
                        );

                    $outReverse = in_array($this->move_type, [MoveType::OUT_INVOICE, MoveType::OUT_RECEIPT])
                        && (
                            $reverseMoveTypes === [MoveType::OUT_REFUND] 
                            || (
                                count($reverseMoveTypes) === 2
                                && in_array(MoveType::OUT_REFUND, $reverseMoveTypes)
                                && in_array(MoveType::ENTRY, $reverseMoveTypes)
                            )
                        );

                    $miscReverse = in_array($this->move_type, [MoveType::ENTRY, MoveType::OUT_REFUND, MoveType::IN_REFUND])
                        && $reverseMoveTypes === ['entry'];
                    
                    if ($inReverse || $outReverse || $miscReverse) {
                        $newPaymentState = PaymentState::REVERSED;
                    }
                }
            } elseif ($this->matchedPayments->filter(function ($payment) {
                return ! $payment->move_id && $payment->state === PaymentStatus::IN_PROCESS;
            })->isNotEmpty()) {
                $newPaymentState = $this->getInvoiceInPaymentState();
            } elseif (! empty($reconciliationVals)) {
                $newPaymentState = PaymentState::PARTIAL;
            } elseif ($this->matchedPayments->filter(function ($payment) {
                return ! $payment->move_id && $payment->state === PaymentStatus::PAID;
            })->isNotEmpty()) {
                $newPaymentState = $this->getInvoiceInPaymentState();
            }
        }
        
        $this->payment_state = $newPaymentState;
    }

    public function getInstallmentsData($lines, $paymentDate = null, $nextPaymentDate = null)
    {
        $paymentDate = $paymentDate ?: now()->toDateString();

        $termLines = $lines->sortBy(function($line) {
            return [$line->date_maturity, $line->date];
        });

        $sign = $this->direction_sign;

        $installments = [];

        $firstInstallmentMode = false;

        $currentInstallmentMode = false;
        
        $i = 1;

        foreach ($termLines as $line) {
            $installment = [
                'number' => $i,
                'line' => $line,
                'date_maturity' => $line->date_maturity ?: $line->date,
                'amount_residual_currency' => $line->amount_residual_currency,
                'amount_residual' => $line->amount_residual,
                'amount_residual_currency_unsigned' => -$sign * $line->amount_residual_currency,
                'amount_residual_unsigned' => -$sign * $line->amount_residual,
                'type' => 'other',
                'reconciled' => $line->reconciled,
            ];

            $installments[] = $installment;

            if ($line->reconciled) {
                $i++;

                continue;
            }

            if ($line->display_type == DisplayType::PAYMENT_TERM) {
                if ($nextPaymentDate && ($line->date_maturity ?: $line->date) <= $nextPaymentDate) {
                    $currentInstallmentMode = 'before_date';
                } elseif (($line->date_maturity ?: $line->date) < $paymentDate) {
                    $firstInstallmentMode = $currentInstallmentMode = 'overdue';
                } elseif (! $firstInstallmentMode) {
                    $firstInstallmentMode = 'next';
                    $currentInstallmentMode = 'next';
                } elseif ($currentInstallmentMode == 'overdue') {
                    $currentInstallmentMode = 'next';
                }

                $installment['type'] = $currentInstallmentMode;

                $installments[count($installments) - 1] = $installment;
            }
            
            $i++;
        }

        return $installments;
    }

    public function getReconcilablePayments()
    {
        $paymentVals = [
            'outstanding' => false,
            'content' => [],
            'move_id' => $this->id,
            'title' => $this->isInbound() ? 'Outstanding debits' : 'Outstanding credits',
        ];

        if (
            $this->state != MoveState::POSTED
            || ! in_array($this->payment_state, [PaymentState::NOT_PAID, PaymentState::PARTIAL])
            || ! $this->isInvoice(true)
        ) {
            return $paymentVals;
        }

        $paymentTermLines = $this->lines->filter(function($line) {
            return in_array($line->account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE]);
        });

        $filters = [
            ['account_id', 'in', $paymentTermLines->pluck('account_id')->toArray()],
            ['parent_state', MoveState::POSTED],
            ['partner_id', $this->commercial_partner_id],
            ['reconciled', false],
        ];

        $filters[] = $this->isInbound() ? ['balance', '<', 0.0]: ['balance', '>', 0.0];
        
        $outstandingLines = MoveLine::where($filters)
            ->where(function($query) {
                $query->where('amount_residual', '!=', 0.0)
                    ->orWhere('amount_residual_currency', '!=', 0.0);
            })
            ->get();

        foreach ($outstandingLines as $line) {
            if ($line->currency_id == $this->currency_id) {
                $amount = abs($line->amount_residual_currency);
            } else {
                $amount = $line->companyCurrency->convert(
                    abs($line->amount_residual),
                    $this->currency,
                    $this->company,
                    $line->date
                );
            }

            if ($this->currency->isZero($amount)) {
                continue;
            }

            $paymentVals['outstanding'] = true;

            $paymentVals['content'][] = [
                'journal_name' => $line->ref ?: $line->move->name,
                'amount' => $amount,
                'currency_id' => $this->currency_id,
                'id' => $line->id,
                'move_id' => $line->move_id,
                'date' => $line->date->toDateString(),
                'account_payment_id' => $line->payment_id,
            ];
        }

        return $paymentVals;
    }
}
