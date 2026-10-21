<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\PointOfSale\Database\Factories\PaymentMethodFactory;
use Webkul\PointOfSale\Enums\PaymentMethodType;
use Webkul\PointOfSale\Enums\PaymentTerminalType;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class PaymentMethod extends Model implements Sortable
{
    use BelongsToCompany, HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'pos_payment_methods';

    protected $fillable = [
        'name',
        'type',
        'terminal_type',
        'sort',
        'image',
        'is_cash_count',
        'is_split_transaction',
        'journal_id',
        'receivable_account_id',
        'outstanding_account_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'type'                 => PaymentMethodType::class,
        'terminal_type'        => PaymentTerminalType::class,
        'is_cash_count'        => 'boolean',
        'is_split_transaction' => 'boolean',
    ];

    protected $attributes = [
        'type'                 => 'cash',
        'terminal_type'        => 'none',
        'is_cash_count'        => false,
        'is_split_transaction' => false,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function receivableAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'receivable_account_id');
    }

    public function outstandingAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'outstanding_account_id');
    }

    public function configs(): BelongsToMany
    {
        return $this->belongsToMany(Config::class, 'pos_config_payment_methods', 'payment_method_id', 'config_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function openSessionNames(): array
    {
        return Session::withoutGlobalScopes()
            ->whereIn('config_id', $this->configs()->pluck('pos_configs.id'))
            ->where('state', '!=', SessionState::CLOSED)
            ->pluck('name')
            ->all();
    }

    public function assertNotInOpenSession(): void
    {
        $sessions = $this->openSessionNames();

        if ($sessions === []) {
            return;
        }

        throw new PosConfigurationException(
            __('point-of-sale::system.payment-method.in-open-session', ['sessions' => implode(', ', $sessions)])
        );
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    public function computeType(): void
    {
        $this->type = match ($this->journal?->type) {
            JournalType::CASH                           => PaymentMethodType::CASH,
            JournalType::BANK, JournalType::CREDIT_CARD => PaymentMethodType::BANK,
            default                                     => PaymentMethodType::PAY_LATER,
        };
    }

    public function computeIsCashCount(): void
    {
        $this->is_cash_count = $this->type === PaymentMethodType::CASH;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (PaymentMethod $paymentMethod) {
            $paymentMethod->computeCreatorId();
        });

        static::deleting(function (PaymentMethod $paymentMethod) {
            $paymentMethod->assertNotInOpenSession();
        });

        static::saving(function (PaymentMethod $paymentMethod) {
            $paymentMethod->unsetRelation('journal');

            $paymentMethod->computeType();

            $paymentMethod->computeIsCashCount();
        });
    }

    protected static function newFactory(): PaymentMethodFactory
    {
        return PaymentMethodFactory::new();
    }
}
