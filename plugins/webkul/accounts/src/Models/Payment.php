<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'created_by',
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

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'                    => __('accounts::models/payment.log-attributes.name'),
            'move.name'               => __('accounts::models/payment.log-attributes.move'),
            'company.name'            => __('accounts::models/payment.log-attributes.company'),
            'partner.name'            => __('accounts::models/payment.log-attributes.partner'),
            'partner_type'            => __('accounts::models/payment.log-attributes.partner_type'),
            'paymentMethod.name'      => __('accounts::models/payment.log-attributes.payment_method'),
            'currency.name'           => __('accounts::models/payment.log-attributes.currency'),
            'paymentToken'            => __('accounts::models/payment.log-attributes.payment_token'),
            'sourcePayment.name'      => __('accounts::models/payment.log-attributes.source_payment'),
            'paymentTransaction.name' => __('accounts::models/payment.log-attributes.payment_transaction'),
            'destinationAccount.name' => __('accounts::models/payment.log-attributes.destination_account'),
            'outstandingAccount.name' => __('accounts::models/payment.log-attributes.outstanding_account'),
            'is_sent'                 => __('accounts::models/payment.log-attributes.is_sent'),
            'state'                   => __('accounts::models/payment.log-attributes.state'),
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function accountMovePayment()
    {
        return $this->belongsToMany(Move::class, 'accounts_accounts_move_payment', 'payment_id', 'invoice_id');
    }
}
