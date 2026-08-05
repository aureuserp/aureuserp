<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Database\Factories\MoveFactory;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Exceptions\MissingJournalException;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Partner\Models\BankAccount;
use Webkul\Security\Models\User;
use Webkul\Security\Support\OwnerSource;
use Webkul\Security\Traits\HasOwnershipScope;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Models\UTMMedium;
use Webkul\Support\Models\UTMSource;
use Webkul\Support\Services\SequenceService;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class Move extends Model implements Sortable
{
    use BelongsToCompany;
    use ChecksCompanyConsistency;
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, HasOwnershipScope, SortableTrait;

    public const ACTIVITY_PLAN_PLUGIN = 'accounts';

    protected $table = 'accounts_account_moves';

    public function getModelTitle(): string
    {
        return match ($this->move_type) {
            MoveType::OUT_INVOICE => __('accounts::models/move.titles.invoice'),
            MoveType::OUT_REFUND  => __('accounts::models/move.titles.credit-note'),
            MoveType::IN_INVOICE  => __('accounts::models/move.titles.bill'),
            MoveType::IN_REFUND   => __('accounts::models/move.titles.refund'),
            default               => __('accounts::models/move.title'),
        };
    }

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

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'                     => __('accounts::models/move.log-attributes.name'),
            'reference'                => __('accounts::models/move.log-attributes.reference'),
            'date'                     => __('accounts::models/move.log-attributes.date'),
            'state'                    => __('accounts::models/move.log-attributes.state'),
            'move_type'                => __('accounts::models/move.log-attributes.move-type'),
            'checked'                  => __('accounts::models/move.log-attributes.checked'),
            'payment_reference'        => __('accounts::models/move.log-attributes.payment-reference'),
            'payment_state'            => __('accounts::models/move.log-attributes.payment-state'),
            'amount_untaxed'           => __('accounts::models/move.log-attributes.amount-untaxed'),
            'invoice_source_email'     => __('accounts::models/move.log-attributes.invoice-source-email'),
            'is_move_sent'             => __('accounts::models/move.log-attributes.is-move-sent'),
            'invoice_origin'           => __('accounts::models/move.log-attributes.invoice-origin'),
            'currency.name'            => __('accounts::models/move.log-attributes.currency'),
            'partner.name'             => __('accounts::models/move.log-attributes.partner'),
            'partnerBank.name'         => __('accounts::models/move.log-attributes.partner-bank'),
            'invoiceUser.name'         => __('accounts::models/move.log-attributes.invoice-user'),
            'fiscalPosition.name'      => __('accounts::models/move.log-attributes.fiscal-position'),
            'invoicePaymentTerm.name'  => __('accounts::models/move.log-attributes.invoice-payment-term'),
            'invoiceCashRounding.name' => __('accounts::models/move.log-attributes.invoice-cash-rounding'),
        ];
    }

    protected $casts = [
        'checked'                           => 'boolean',
        'invoice_date_due'                  => 'date',
        'amount_tax'                        => 'decimal:4',
        'amount_total'                      => 'decimal:4',
        'amount_residual'                   => 'decimal:4',
        'amount_untaxed'                    => 'decimal:4',
        'amount_tax_signed'                 => 'decimal:4',
        'amount_residual_signed'            => 'decimal:4',
        'amount_untaxed_signed'             => 'decimal:4',
        'amount_total_in_currency_signed'   => 'decimal:4',
        'amount_untaxed_in_currency_signed' => 'decimal:4',
        'amount_total_signed'               => 'decimal:4',
        'state'                             => MoveState::class,
        'payment_state'                     => PaymentState::class,
        'move_type'                         => MoveType::class,
        'invoice_date'                      => 'date',
        'date'                              => 'date',
    ];

    public $typeReverseMapping = [
        MoveType::ENTRY->value       => MoveType::ENTRY,
        MoveType::OUT_INVOICE->value => MoveType::OUT_REFUND,
        MoveType::OUT_REFUND->value  => MoveType::OUT_INVOICE,
        MoveType::IN_INVOICE->value  => MoveType::IN_REFUND,
        MoveType::IN_REFUND->value   => MoveType::IN_INVOICE,
        MoveType::OUT_RECEIPT->value => MoveType::OUT_REFUND,
        MoveType::IN_RECEIPT->value  => MoveType::IN_REFUND,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function ownershipSources(): array
    {
        return [
            OwnerSource::column('creator_id'),
            OwnerSource::column('invoice_user_id'),
        ];
    }

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

    public function originPayment()
    {
        return $this->belongsTo(Payment::class, 'origin_payment_id');
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

    public function creator(): BelongsTo
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
        return $this->belongsToMany(Payment::class, 'accounts_accounts_move_payment', 'invoice_id', 'payment_id');
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
            $move->computeCreatorId();

            $move->computeCompanyId();

            $move->computeCurrencyId();

            $move->date ??= now();
        });

        static::created(function ($move) {
            $move->computeName();

            $move->saveQuietly();
        });

        static::saving(function ($move) {
            $move->computeCreatorId();

            $move->computeCompanyId();

            $move->computeCurrencyId();

            $move->computePartnerDisplayInfo();

            $move->computePartnerShippingId();

            $move->computeCommercialPartnerId();

            $move->computeJournalId();

            $move->computeInvoiceCurrencyRate();

            $move->computeInvoiceDateDue();

            $move->computePaymentState();
        });
    }

    public function computeCreatorId()
    {
        if ($this->creator_id) {
            return;
        }

        $this->creator_id ??= Auth::id();
    }

    public function computeCompanyId()
    {
        if ($this->company_id) {
            return;
        }

        $this->company_id ??= current_company_id();
    }

    public function computeName()
    {
        if (filled($this->name)) {
            return;
        }

        if (! $this->journal) {
            return;
        }

        $variants = [];

        if (
            $this->journal->refund_sequence
            && in_array($this->move_type, [MoveType::OUT_REFUND, MoveType::IN_REFUND])
        ) {
            $variants[] = 'refund';
        }

        if ($this->journal->payment_sequence && $this->origin_payment_id) {
            $variants[] = 'payment';
        }

        $prefix = implode('', array_map(fn (string $variant): string => strtoupper($variant[0]), $variants));

        $this->sequence_prefix = sprintf(
            '%s%s/%s',
            $prefix,
            $this->journal->code,
            $this->date?->format('Y') ?? now()->format('Y'),
        );

        $variant = implode('-', $variants);

        $this->name = SequenceService::nextFor(
            $this->journal,
            $variant,
            $this->company_id,
            $this->journal->sequenceDefaults($variant),
            $this->date,
        );
    }

    public function computeCurrencyId()
    {
        if ($this->currency_id) {
            return;
        }

        $this->currency_id = $this->journal->currency_id ?? $this->journal->company->currency_id;
    }

    public function computePartnerDisplayInfo()
    {
        $vendorDisplayName = $this->partner?->name;

        if (! $vendorDisplayName) {
            if ($this->invoice_source_email) {
                $vendorDisplayName = "@From: {$this->invoice_source_email}";
            } else {
                $vendorDisplayName = "#Created by: {$this->creator->name}";
            }
        }

        $this->invoice_partner_display_name = $vendorDisplayName;
    }

    public function computeInvoiceCurrencyRate()
    {
        if (! $this->isInvoice(true)) {
            return 1;
        }

        if ($this->currency_id) {
            $this->invoice_currency_rate = $this->currency->getConversionRate(
                fromCurrency: $this->company->currency,
                toCurrency: $this->currency,
                company: $this->company,
                date: $this->invoice_date ?? now()->toDateString(),
            );
        } else {
            $this->invoice_currency_rate = 1;
        }
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
        if (! $this->invoice_payment_term_id) {
            return;
        }

        $today = now();

        $neededTerms = $this->paymentTermLines->filter(function ($line) {
            return $line->date_maturity !== null;
        });

        if ($neededTerms->isNotEmpty()) {
            $maxDateMaturity = $neededTerms->max('date_maturity');

            $this->invoice_date_due = $maxDateMaturity;
        } elseif (! $this->invoice_date_due) {
            $this->invoice_date_due = $today;
        }
    }

    public function computeJournalId()
    {
        if (! in_array($this->journal?->type, $this->getValidJournalTypes())) {
            $journal = $this->searchDefaultJournal();

            if (! $journal) {
                throw new MissingJournalException(__('accounts::system.move.no-journal-found', [
                    'company' => $this->company?->name ?? $this->company_id,
                    'types'   => collect($this->getValidJournalTypes())->map(fn ($type) => $type->value)->implode(', '),
                ]));
            }

            $this->journal_id = $journal->id;

            $this->unsetRelation('journal');
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
        $needsPaymentState = $this->isInvoice(true);

        $matchings = $this->reconciliationMatchings();

        if ($needsPaymentState) {
            $matchings = array_filter(
                $matchings,
                fn (array $row) => in_array($row['source_line_account_type'], ['asset_receivable', 'liability_payable'])
            );
        }

        $currentState = $this->payment_state !== PaymentState::BLOCKED
            ? PaymentState::NOT_PAID
            : PaymentState::BLOCKED;

        if ($this->state !== MoveState::POSTED || ! $needsPaymentState) {
            $this->payment_state = $currentState;

            return;
        }

        $this->payment_state = $this->settlementCurrency()->isZero($this->amount_residual)
            ? $this->settledPaymentState($matchings)
            : $this->outstandingPaymentState($matchings, $currentState);
    }

    protected function settlementCurrency()
    {
        $currencies = $this->lines->pluck('currency_id')->unique();

        return Currency::find(
            $currencies->count() === 1 ? $currencies->first() : $this->company->currency_id
        );
    }

    protected function settledPaymentState(array $matchings): PaymentState
    {
        $paidByPaymentOrStatement = collect($matchings)
            ->contains(fn (array $row) => $row['has_payment'] || $row['has_statement_line']);

        if ($paidByPaymentOrStatement) {
            return collect($matchings)->every(fn (array $row) => $row['all_payments_matched'])
                ? PaymentState::PAID
                : $this->getInvoiceInPaymentState();
        }

        return $this->isFullyReversedBy($this->counterpartMoveTypes($matchings))
            ? PaymentState::REVERSED
            : PaymentState::PAID;
    }

    protected function outstandingPaymentState(array $matchings, PaymentState $fallback): PaymentState
    {
        $awaitingPayment = fn (PaymentStatus $status) => $this->matchedPayments
            ->contains(fn ($payment) => ! $payment->move_id && $payment->state === $status);

        return match (true) {
            $awaitingPayment(PaymentStatus::IN_PROCESS) => $this->getInvoiceInPaymentState(),
            $matchings !== []                           => PaymentState::PARTIAL,
            $awaitingPayment(PaymentStatus::PAID)       => $this->getInvoiceInPaymentState(),
            default                                     => $fallback,
        };
    }

    protected function counterpartMoveTypes(array $matchings): array
    {
        $types = collect($matchings)
            ->flatMap(fn (array $row) => $row['opposite_move_types'])
            ->unique()
            ->values()
            ->all();

        sort($types);

        return $types;
    }

    protected function isFullyReversedBy(array $counterpartTypes): bool
    {
        $reversedBy = function (MoveType $refund) use ($counterpartTypes) {
            return $counterpartTypes === [$refund->value]
                || (count($counterpartTypes) === 2
                    && in_array($refund->value, $counterpartTypes)
                    && in_array(MoveType::ENTRY->value, $counterpartTypes));
        };

        return match (true) {
            in_array($this->move_type, [MoveType::IN_INVOICE, MoveType::IN_RECEIPT])                 => $reversedBy(MoveType::IN_REFUND),
            in_array($this->move_type, [MoveType::OUT_INVOICE, MoveType::OUT_RECEIPT])               => $reversedBy(MoveType::OUT_REFUND),
            in_array($this->move_type, [MoveType::ENTRY, MoveType::OUT_REFUND, MoveType::IN_REFUND]) => $counterpartTypes === [MoveType::ENTRY->value],
            default                                                                                  => false,
        };
    }

    protected function reconciliationMatchings(): array
    {
        $rows = $this->reconciliationSideQuery('debit_move_id', 'credit_move_id')
            ->get()
            ->merge($this->reconciliationSideQuery('credit_move_id', 'debit_move_id')->get());

        return $rows->map(function ($row) {
            $counterpartTypes = is_string($row->opposite_move_types)
                ? json_decode($row->opposite_move_types, true) ?? []
                : $row->opposite_move_types;

            return [
                'source_line_id'           => $row->source_line_id,
                'source_move_id'           => $row->source_move_id,
                'source_line_account_type' => $row->source_line_account_type,
                'opposite_move_types'      => $counterpartTypes,
                'all_payments_matched'     => (bool) $row->all_payments_matched,
                'has_payment'              => (bool) $row->has_payment,
                'has_statement_line'       => (bool) $row->has_statement_line,
            ];
        })->all();
    }

    protected function reconciliationSideQuery(string $sourceColumn, string $oppositeColumn)
    {
        return DB::table('accounts_partial_reconciles as partial_reconciles')
            ->select([
                'source_line.id as source_line_id',
                'source_line.move_id as source_move_id',
                'account.account_type as source_line_account_type',
                ...$this->reconciliationAggregateSelects(),
            ])
            ->join('accounts_account_move_lines as source_line', 'source_line.id', '=', "partial_reconciles.{$sourceColumn}")
            ->join('accounts_accounts as account', 'account.id', '=', 'source_line.account_id')
            ->join('accounts_account_move_lines as opposite_line', 'opposite_line.id', '=', "partial_reconciles.{$oppositeColumn}")
            ->join('accounts_account_moves as opposite_move', 'opposite_move.id', '=', 'opposite_line.move_id')
            ->leftJoin('accounts_account_payments as payment', 'payment.id', '=', 'opposite_move.origin_payment_id')
            ->where('source_line.move_id', $this->id)
            ->whereColumn('opposite_line.move_id', '!=', 'source_line.move_id')
            ->groupBy('source_line.id', 'source_line.move_id', 'account.account_type');
    }

    /**
     * Shared aggregate SELECT expressions for the debit/credit reconciliation
     * queries in computePaymentState(). Aggregate functions over boolean
     * expressions (SUM/COALESCE) are MySQL-lenient but rejected by PostgreSQL's
     * stricter type system, so every aggregate is normalized to a plain integer
     * (0/1) here so PHP can cast it to bool consistently regardless of driver.
     * The JSON array aggregation itself goes through db_dialect() since MySQL's
     * JSON_ARRAYAGG has no direct PostgreSQL equivalent (json_agg).
     */
    private function reconciliationAggregateSelects(): array
    {
        return [
            DB::raw(db_dialect()->jsonArrayAgg('opposite_move.move_type').' as opposite_move_types'),
            DB::raw('
                CASE
                    WHEN SUM(CASE WHEN opposite_move.origin_payment_id IS NOT NULL THEN 1 ELSE 0 END) = 0
                        THEN 1
                    ELSE MIN(CASE WHEN COALESCE(payment.is_matched, false) THEN 1 ELSE 0 END)
                END AS all_payments_matched
            '),
            DB::raw('MAX(CASE WHEN payment.id IS NOT NULL THEN 1 ELSE 0 END) as has_payment'),
            DB::raw('MAX(CASE WHEN opposite_move.statement_line_id IS NOT NULL THEN 1 ELSE 0 END) as has_statement_line'),
        ];
    }

    public function getInstallmentsData($lines, $paymentDate = null, $nextPaymentDate = null)
    {
        $paymentDate = $paymentDate ?: now()->toDateString();

        $termLines = $lines->sortBy(function ($line) {
            return [$line->date_maturity, $line->date];
        });

        $sign = $this->direction_sign;

        $installments = [];

        $firstInstallmentMode = false;

        $currentInstallmentMode = false;

        $i = 1;

        foreach ($termLines as $line) {
            $installment = [
                'number'                            => $i,
                'line'                              => $line,
                'date_maturity'                     => $line->date_maturity ?: $line->date,
                'amount_residual_currency'          => $line->amount_residual_currency,
                'amount_residual'                   => $line->amount_residual,
                'amount_residual_currency_unsigned' => -$sign * $line->amount_residual_currency,
                'amount_residual_unsigned'          => -$sign * $line->amount_residual,
                'type'                              => 'other',
                'reconciled'                        => $line->reconciled,
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
            'title'       => $this->isInbound() ? 'Outstanding credits' : 'Outstanding debits',
            'move_id'     => $this->id,
            'outstanding' => false,
            'lines'       => [],
        ];

        if (
            $this->state != MoveState::POSTED
            || ! in_array($this->payment_state, [PaymentState::NOT_PAID, PaymentState::PARTIAL])
            || ! $this->isInvoice(true)
        ) {

            return $paymentVals;
        }

        $paymentTermLines = $this->lines->filter(function ($line) {
            return in_array($line->account?->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE]);
        });

        $outstandingLines = MoveLine::whereIn('account_id', $paymentTermLines->pluck('account_id'))
            ->where('parent_state', MoveState::POSTED)
            ->where('partner_id', $this->commercial_partner_id)
            ->where('reconciled', false)
            ->where(function ($query) {
                $query->where('amount_residual', '!=', 0.0)
                    ->orWhere('amount_residual_currency', '!=', 0.0);
            })
            ->when($this->isInbound(), function ($query) {
                return $query->where('balance', '<', 0);
            }, function ($query) {
                return $query->where('balance', '>', 0);
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

            $paymentVals['lines'][] = [
                'journal_name'       => $line->ref ?: $line->move->name,
                'amount'             => $amount,
                'currency'           => $this->currency,
                'id'                 => $line->id,
                'move_id'            => $line->move_id,
                'move_type'          => $line->move->move_type,
                'date'               => $line->date->toDateString(),
                'account_payment_id' => $line->payment_id,
            ];
        }

        return $paymentVals;
    }

    public function getReconciledPayments()
    {
        $paymentVals = [
            'title'       => 'Less Payment',
            'outstanding' => false,
            'lines'       => [],
        ];

        if ($this->state !== MoveState::POSTED || ! $this->isInvoice(true)) {
            return $paymentVals;
        }

        $reconciledPartials = $this->getAllReconciledInvoicePartials();

        foreach ($reconciledPartials as $reconciledPartial) {
            $counterpartLine = $reconciledPartial['line'];

            if ($counterpartLine->move->ref) {
                $reconciliationRef = sprintf('%s (%s)', $counterpartLine->move->name, $counterpartLine->move->ref);
            } else {
                $reconciliationRef = $counterpartLine->move->name;
            }

            if ($counterpartLine->amount_currency && $counterpartLine->currency_id != $counterpartLine->company->currency_id) {
                $foreignCurrency = $counterpartLine->currency;
            } else {
                $foreignCurrency = false;
            }

            $paymentVals['lines'][] = [
                'name'         => $counterpartLine->name,
                'journal_name' => $counterpartLine->journal->name,
                'company_name' => $counterpartLine->journal->company_id != $this->company_id
                    ? $counterpartLine->journal->company->name
                    : false,
                'amount'                  => $reconciledPartial['amount'],
                'amount_currency'         => money($reconciledPartial['amount'], $counterpartLine->currency->name),
                'currency_id'             => $reconciledPartial['is_exchange']
                    ? $this->company->currency_id
                    : $reconciledPartial['currency']->id,
                'date'                    => $counterpartLine->date,
                'partial_id'              => $reconciledPartial['partial_id'],
                'account_payment_id'      => $counterpartLine->payment_id,
                'payment_method_name'     => $counterpartLine->payment?->paymentMethodLine->name,
                'move_id'                 => $counterpartLine->move_id,
                'move_type'               => $counterpartLine->move->move_type,
                'ref'                     => $reconciliationRef,
                'is_exchange'             => $reconciledPartial['is_exchange'],
                'amount_company_currency' => money(abs($counterpartLine->balance), $counterpartLine->company->currency->name),
                'amount_foreign_currency' => $foreignCurrency
                    ? money(abs($counterpartLine->amount_currency), $foreignCurrency->name)
                    : null,
            ];
        }

        return $paymentVals;
    }

    protected function getAllReconciledInvoicePartials()
    {
        $lineIds = $this->lines
            ->filter(fn ($line) => in_array($line->account?->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE]))
            ->pluck('id');

        if ($lineIds->isEmpty()) {
            return [];
        }

        $partials = $this->partialsFacing($lineIds);

        $matched = $partials->map(fn ($partial) => [
            'line_id'    => $partial->counterpart_line_id,
            'partial_id' => $partial->id,
            'amount'     => $partial->amount,
            'currency'   => $this->currency,
        ])->all();

        $counterpartLineIds = $partials->pluck('counterpart_line_id')->all();

        $exchangeMoveIds = $partials->pluck('exchange_move_id')->filter()->unique()->values()->all();

        foreach ($this->exchangePartials($exchangeMoveIds, $counterpartLineIds) as $partial) {
            $counterpartLineIds[] = $partial->counterpart_line_id;

            $matched[] = [
                'line_id'    => $partial->counterpart_line_id,
                'partial_id' => $partial->id,
                'currency'   => $this->company->currency_id,
            ];
        }

        $counterpartLines = MoveLine::whereIn('id', array_unique($counterpartLineIds))->get()->keyBy('id');

        return array_map(function (array $values) use ($counterpartLines, $exchangeMoveIds) {
            $values['line'] = $counterpartLines[$values['line_id']];

            $values['is_exchange'] = in_array($values['line']->move_id, $exchangeMoveIds);

            if ($values['is_exchange']) {
                $values['amount'] = abs($values['line']->balance);
            }

            return $values;
        }, $matched);
    }

    protected function partialsFacing($lineIds)
    {
        $side = fn (string $ownColumn, string $counterpartColumn, string $amountColumn) => DB::table('accounts_partial_reconciles')
            ->select([
                'id',
                'exchange_move_id',
                "{$amountColumn} as amount",
                "{$counterpartColumn} as counterpart_line_id",
            ])
            ->whereIn($ownColumn, $lineIds);

        return $side('debit_move_id', 'credit_move_id', 'debit_amount_currency')
            ->unionAll($side('credit_move_id', 'debit_move_id', 'credit_amount_currency'))
            ->get();
    }

    protected function exchangePartials(array $exchangeMoveIds, array $counterpartLineIds)
    {
        if ($exchangeMoveIds === [] || $counterpartLineIds === []) {
            return collect();
        }

        $side = fn (string $joinColumn, string $counterpartColumn, string $facingColumn) => DB::table('accounts_partial_reconciles')
            ->select(['accounts_partial_reconciles.id', "accounts_partial_reconciles.{$counterpartColumn} as counterpart_line_id"])
            ->join(
                'accounts_account_move_lines',
                'accounts_account_move_lines.id',
                '=',
                "accounts_partial_reconciles.{$joinColumn}"
            )
            ->whereIn('accounts_account_move_lines.move_id', $exchangeMoveIds)
            ->whereIn("accounts_partial_reconciles.{$facingColumn}", array_unique($counterpartLineIds));

        return $side('credit_move_id', 'credit_move_id', 'debit_move_id')
            ->unionAll($side('debit_move_id', 'debit_move_id', 'credit_move_id'))
            ->get();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MoveFactory::new();
    }

    public function companyConsistentFields(): array
    {
        return [
            'journal_id'              => Journal::class,
            'invoice_payment_term_id' => PaymentTerm::class,
            'fiscal_position_id'      => FiscalPosition::class,
        ];
    }
}
