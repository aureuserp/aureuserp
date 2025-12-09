<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Enums\JournalType;
use Webkul\Partner\Models\BankAccount;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Account\Settings\DefaultAccountSettings;
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

    public function creator()
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

    public function moveLines()
    {
        return $this->hasMany(MoveLine::class, 'journal_id');
    }

    public function inboundPaymentMethodLines(): HasMany
    {
        return $this->hasMany(PaymentMethodLine::class)
            ->whereHas('paymentMethod', function ($q) {
                $q->where('payment_type', 'inbound');
            });
    }

    public function outboundPaymentMethodLines(): HasMany
    {
        return $this->hasMany(PaymentMethodLine::class)
            ->whereHas('paymentMethod', function ($q) {
                $q->where('payment_type', 'outbound');
            });
    }

    public function getAvailablePaymentMethodLines(string $paymentType)
    {
        if ($paymentType == 'inbound') {
            return $this->inboundPaymentMethodLines;
        } else {
            return $this->outboundPaymentMethodLines;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($journal) {
            $journal->computeSuspenseAccountId();

            $journal->syncInboundPaymentMethodLines();

            $journal->syncOutboundPaymentMethodLines();
        });
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

    public function syncInboundPaymentMethodLines()
    {
        $this->inboundPaymentMethodLines()->delete();

        if (in_array($this->type, [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])) {
            $defaultMethods = PaymentMethod::where('code', 'manual')
                ->where('payment_type', 'inbound')
                ->get();

            foreach ($defaultMethods as $method) {
                $this->inboundPaymentMethodLines()->updateOrCreate([
                    'payment_method_id' => $method->id,
                ], [
                    'name' => $method->name,
                ]);
            }
        }
    }

    public function syncOutboundPaymentMethodLines()
    {
        $this->outboundPaymentMethodLines()->delete();

        if (in_array($this->type, [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])) {
            $defaultMethods = PaymentMethod::where('code', 'manual')
                ->where('payment_type', 'outbound')
                ->get();

            foreach ($defaultMethods as $method) {
                $this->outboundPaymentMethodLines()->updateOrCreate([
                    'payment_method_id' => $method->id,
                ], [
                    'name' => $method->name,
                ]);
            }
        }
    }
}
