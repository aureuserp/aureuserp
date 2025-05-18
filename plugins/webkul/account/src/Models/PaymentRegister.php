<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function registerMoveLines()
    {
        return $this->belongsToMany(MoveLine::class, 'accounts_account_payment_register_move_lines', 'payment_register_id', 'move_line_id');
    }
}
