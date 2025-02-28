<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

class Move extends Model
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity;

    protected $table = 'accounts_account_moves';

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
        'invoice_date_due' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(UtmCampaign::class);
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
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
        return $this->belongsTo(PaymentTerm::class, 'invoice_payment_term_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function commercialPartner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function partnerShipping()
    {
        return $this->belongsTo(Partner::class);
    }

    public function partnerBank()
    {
        return $this->belongsTo(BankAccount::class, 'partner_bank_id');
    }

    public function fiscalPosition()
    {
        return $this->belongsTo(FiscalPosition::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
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
        return $this->belongsTo(UTMSource::class);
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

    public function lines()
    {
        return $this->hasMany(MoveLine::class)
            ->where('display_type', 'product');
    }

    public function allLines()
    {
        return $this->hasMany(MoveLine::class);
    }

    public function taxLines()
    {
        return $this->hasMany(MoveLine::class)
            ->where('display_type', 'tax');
    }

    public function paymentTermLine()
    {
        return $this->hasOne(MoveLine::class)
            ->where('display_type', 'payment_term');
    }

    public static function generateNextInvoiceAndCreditNoteNumber(string $type = 'INV'): string
    {
        $year = date('Y');
        $prefix = "{$type}/{$year}/";

        $lastInvoice = self::whereRaw('name LIKE ?', ["{$prefix}%"])
            ->latest('name')
            ->first();

        $lastNumber = optional($lastInvoice)->name
            ? (int) substr($lastInvoice->name, strlen($prefix))
            : 0;

        return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->name)) {
                $model->name = self::generateNextInvoiceAndCreditNoteNumber();
            }

            $model->sequence_prefix = self::extractPrefixFromName($model->name);
        });
    }

    /**
     * Extracts the prefix (e.g., "INV or RINV/2025/") from the given invoice and credit Note name.
     */
    protected static function extractPrefixFromName(string $name): string
    {
        return substr($name, 0, strrpos($name, '/') + 1);
    }
}
