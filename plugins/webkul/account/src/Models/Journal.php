<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
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

    public function getAvailablePaymentMethodLines(string $paymentType): mixed
    {
        if (! $this->exists) {
            return PaymentMethodLine::query()->whereNull('id')->get();
        }

        return match ($paymentType) {
            'inbound'  => $this->inboundPaymentMethodLines,
            'outbound' => $this->outboundPaymentMethodLines,
            default    => throw new \InvalidArgumentException('Invalid payment type'),
        };
    }

    public function inboundPaymentMethodLines(): HasMany
    {
        return $this->hasMany(PaymentMethodLine::class)->where('type', 'inbound');
    }

    public function outboundPaymentMethodLines(): HasMany
    {
        return $this->hasMany(PaymentMethodLine::class)->where('type', 'outbound');
    }

    public function computeInboundPaymentMethodLines(): void
    {
        if (! in_array($this->type, ['bank', 'cash', 'credit'])) {
            $this->inboundPaymentMethodLines()->delete();

            return;
        }

        DB::transaction(function () {
            $this->inboundPaymentMethodLines()->delete();

            $defaultMethods = $this->getDefaultInboundPaymentMethods();

            foreach ($defaultMethods as $method) {
                $this->inboundPaymentMethodLines()->create([
                    'name'              => $method->name,
                    'payment_method_id' => $method->id,
                    'type'              => 'inbound',
                ]);
            }
        });
    }

    protected function getDefaultInboundPaymentMethods(): mixed
    {
        return PaymentMethod::where('type', 'inbound')
            ->where('active', true)
            ->get();
    }
}
