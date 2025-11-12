<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
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

    public function getModelTitle(): string
    {
        return __('accounts::models/move.title');
    }

    protected $fillable = [
        'sort',
        'journal_id',
        'company_id',
        'campaign_id',
        'tax_cash_basis_origin_move_id',
        'auto_post_origin_id',
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
            'medium.name'                       => __('accounts::models/move.log-attributes.medium'),
            'source.name'                       => __('accounts::models/move.log-attributes.source'),
            'partner.name'                      => __('accounts::models/move.log-attributes.partner'),
            'commercialPartner.name'            => __('accounts::models/move.log-attributes.commercialPartner'),
            'partnerShipping.name'              => __('accounts::models/move.log-attributes.partnerShipping'),
            'partnerBank.name'                  => __('accounts::models/move.log-attributes.partnerBank'),
            'fiscalPosition.name'               => __('accounts::models/move.log-attributes.fiscalPosition'),
            'currency.name'                     => __('accounts::models/move.log-attributes.currency'),
            'reversedEntry.name'                => __('accounts::models/move.log-attributes.reversedEntry'),
            'invoiceUser.name'                  => __('accounts::models/move.log-attributes.invoiceUser'),
            'invoiceIncoterm.name'              => __('accounts::models/move.log-attributes.invoiceIncoterm'),
            'invoiceCashRounding.name'          => __('accounts::models/move.log-attributes.invoiceCashRounding'),
            'createdBy.name'                    => __('accounts::models/move.log-attributes.createdBy'),
            'name'                              => __('accounts::models/move.log-attributes.name'),
            'state'                             => __('accounts::models/move.log-attributes.state'),
            'reference'                         => __('accounts::models/move.log-attributes.reference'),
            'invoiceSourceEmail'                => __('accounts::models/move.log-attributes.invoiceSourceEmail'),
            'invoicePartnerDisplayName'         => __('accounts::models/move.log-attributes.invoicePartnerDisplayName'),
            'invoiceOrigin'                     => __('accounts::models/move.log-attributes.invoiceOrigin'),
            'incotermLocation'                  => __('accounts::models/move.log-attributes.incotermLocation'),
            'date'                              => __('accounts::models/move.log-attributes.date'),
            'invoice_date'                      => __('accounts::models/move.log-attributes.invoice_date'),
            'invoice_date_due'                  => __('accounts::models/move.log-attributes.invoice_date_due'),
            'delivery_date'                     => __('accounts::models/move.log-attributes.delivery_date'),
            'narration'                         => __('accounts::models/move.log-attributes.narration'),
            'amount_untaxed'                    => __('accounts::models/move.log-attributes.amount_untaxed'),
            'amount_tax'                        => __('accounts::models/move.log-attributes.amount_tax'),
            'amount_total'                      => __('accounts::models/move.log-attributes.amount_total'),
            'amount_residual'                   => __('accounts::models/move.log-attributes.amount_residual'),
            'amount_untaxed_signed'             => __('accounts::models/move.log-attributes.amount_untaxed_signed'),
            'amount_untaxed_in_currency_signed' => __('accounts::models/move.log-attributes.amount_untaxed_in_currency_signed'),
            'amount_tax_signed'                 => __('accounts::models/move.log-attributes.amount_tax_signed'),
            'amount_total_signed'               => __('accounts::models/move.log-attributes.amount_total_signed'),
            'amount_total_in_currency_signed'   => __('accounts::models/move.log-attributes.amount_total_in_currency_signed'),
            'amount_residual_signed'            => __('accounts::models/move.log-attributes.amount_residual_signed'),
            'quick_edit_total_amount'           => __('accounts::models/move.log-attributes.quick_edit_total_amount'),
            'is_storno'                         => __('accounts::models/move.log-attributes.is_storno'),
            'always_tax_exigible'               => __('accounts::models/move.log-attributes.always_tax_exigible'),
            'checked'                           => __('accounts::models/move.log-attributes.checked'),
            'posted_before'                     => __('accounts::models/move.log-attributes.posted_before'),
            'made_sequence_gap'                 => __('accounts::models/move.log-attributes.made_sequence_gap'),
            'is_manually_modified'              => __('accounts::models/move.log-attributes.is_manually_modified'),
            'is_move_sent'                      => __('accounts::models/move.log-attributes.is_move_sent'),
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

    public function lines()
    {
        return $this->hasMany(MoveLine::class, 'move_id')
            ->where('display_type', 'product');
    }

    public function allLines()
    {
        return $this->hasMany(MoveLine::class, 'move_id');
    }

    public function taxLines()
    {
        return $this->hasMany(MoveLine::class, 'move_id')
            ->where('display_type', 'tax');
    }

    public function paymentTermLine()
    {
        return $this->hasOne(MoveLine::class, 'move_id')
            ->where('display_type', 'payment_term');
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

    /**
     * Bootstrap any application services.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->creator_id = auth()->id();
        });

        static::created(function ($model) {
            $model->updateSequencePrefix();

            $model->updateQuietly([
                'name' => $model->sequence_prefix.'/'.$model->id,
            ]);
        });
    }

    /**
     * Update the full name without triggering additional events
     */
    public function updateSequencePrefix()
    {
        $suffix = date('Y').'/'.date('m');

        switch ($this->move_type) {
            case MoveType::OUT_INVOICE:
                $this->sequence_prefix = 'INV/'.$suffix;

                break;
            case MoveType::OUT_REFUND:
                $this->sequence_prefix = 'RINV/'.$suffix;

                break;
            case MoveType::IN_INVOICE:
                $this->sequence_prefix = 'BILL/'.$suffix;

                break;
            case MoveType::IN_REFUND:
                $this->sequence_prefix = 'RBILL/'.$suffix;

                break;
            default:
                $this->sequence_prefix = $suffix;

                break;
        }
    }
}
