<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    use HasFactory;

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
        return $this->belongsTo(Move::class, 'reversed_entry_id');
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

    public function lines()
    {
        return $this->hasMany(MoveLine::class)
            ->where('display_type', 'product');
    }

    public static function generateNextInvoiceNumber()
    {
        $lastInvoice = self::whereNotNull('name')
            ->where('name', 'like', 'INV/%')
            ->orderBy('name', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->name, strrpos($lastInvoice->name, '/') + 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'INV/' . date('Y') . '/' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->name)) {
                $model->name = self::generateNextInvoiceNumber();
            }
        });
    }
}
