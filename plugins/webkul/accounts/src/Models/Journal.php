<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Database\Factories\JournalFactory;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Partner\Models\BankAccount;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class Journal extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'accounts_journals';

    protected $fillable = [
        'default_account_id',
        'suspense_account_id',
        'sort',
        'currency_id',
        'company_id',
        'profit_account_id',
        'loss_account_id',
        'bank_account_id',
        'creator_id',
        'color',
        'access_token',
        'code',
        'type',
        'invoice_reference_type',
        'invoice_reference_model',
        'bank_statements_source',
        'name',
        'order_override_regex',
        'auto_check_on_post',
        'restrict_mode_hash_table',
        'refund_order',
        'payment_order',
        'show_on_dashboard',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    protected $casts = [
        'type' => JournalType::class,
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function defaultAccount()
    {
        return $this->belongsTo(Account::class, 'default_account_id');
    }

    public function lossAccount()
    {
        return $this->belongsTo(Account::class, 'loss_account_id');
    }

    public function profitAccount()
    {
        return $this->belongsTo(Account::class, 'profit_account_id');
    }

    public function suspenseAccount()
    {
        return $this->belongsTo(Account::class, 'suspense_account_id');
    }

    public function allowedAccounts()
    {
        return $this->belongsToMany(Account::class, 'accounts_journal_accounts', 'journal_id', 'account_id');
    }

    public function moves()
    {
        return $this->hasMany(Move::class, 'journal_id');
    }

    public function moveLines()
    {
        return $this->hasMany(MoveLine::class, 'journal_id');
    }

    public function getPaymentSequenceAttribute()
    {
        if (in_array($this->type, [JournalType::CASH, JournalType::BANK, JournalType::CREDIT_CARD])) {
            return true;
        }

        return false;
    }

    public function getRefundSequenceAttribute()
    {
        if (in_array($this->type, [JournalType::SALE, JournalType::PURCHASE])) {
            return true;
        }

        return false;
    }

    public function inboundPaymentMethodLines(): HasMany
    {
        return $this->hasMany(PaymentMethodLine::class)
            ->whereHas('paymentMethod', function ($q) {
                $q->where('payment_type', PaymentType::RECEIVE);
            });
    }

    public function outboundPaymentMethodLines(): HasMany
    {
        return $this->hasMany(PaymentMethodLine::class)
            ->whereHas('paymentMethod', function ($q) {
                $q->where('payment_type', PaymentType::SEND);
            });
    }

    public function getAvailablePaymentMethodLines($paymentType)
    {
        return $paymentType == PaymentType::RECEIVE
            ? $this->inboundPaymentMethodLines
            : $this->outboundPaymentMethodLines;
    }

    public function computeSuspenseAccountId()
    {
        if (! in_array($this->type, [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])) {
            $this->suspense_account_id = null;
        } elseif ($this->suspense_account_id) {
            $this->suspense_account_id = $this->suspense_account_id;
        } elseif ($accountId = (new DefaultAccountSettings)->account_journal_suspense_account_id) {
            $this->suspense_account_id = $accountId;
        } else {
            $this->suspense_account_id = null;
        }
    }

    /**
     * Get default inbound payment method lines data
     */
    public static function getDefaultInboundPaymentMethodLines(): array
    {
        $defaultMethods = PaymentMethod::where('code', 'manual')
            ->where('payment_type', PaymentType::RECEIVE)
            ->get();

        return $defaultMethods->map(function ($method) {
            return [
                'payment_method_id'  => $method->id,
                'name'               => $method->name,
                'payment_account_id' => null,
            ];
        })->toArray();
    }

    /**
     * Get default outbound payment method lines data
     */
    public static function getDefaultOutboundPaymentMethodLines(): array
    {
        $defaultMethods = PaymentMethod::where('code', 'manual')
            ->where('payment_type', PaymentType::SEND)
            ->get();

        return $defaultMethods->map(function ($method) {
            return [
                'payment_method_id'  => $method->id,
                'name'               => $method->name,
                'payment_account_id' => null,
            ];
        })->toArray();
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($journal) {
            $journal->creator_id ??= Auth::id();
        });

        static::saving(function ($journal) {
            $journal->computeSuspenseAccountId();
        });
    }

    protected static function newFactory()
    {
        return JournalFactory::new();
    }
}
