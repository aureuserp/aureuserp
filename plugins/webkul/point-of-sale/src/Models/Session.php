<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\PointOfSale\Database\Factories\SessionFactory;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Enums\StockUpdateMode;
use Webkul\PointOfSale\Settings\InventorySettings;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Services\SequenceService;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class Session extends Model
{
    use BelongsToCompany, ChecksCompanyConsistency, HasChatter, HasCustomFields, HasFactory, HasLogActivity;

    protected $table = 'pos_sessions';

    protected $fillable = [
        'name',
        'state',
        'stock_update_mode',
        'sequence_number',
        'login_number',
        'opening_notes',
        'closing_notes',
        'started_at',
        'stopped_at',
        'cash_balance_start',
        'cash_balance_end_real',
        'cash_balance_end',
        'cash_difference',
        'cash_transaction_total',
        'total_payments_amount',
        'order_count',
        'operation_count',
        'is_rescue',
        'has_failed_operations',
        'config_id',
        'rescue_for_session_id',
        'cash_journal_id',
        'move_id',
        'cogs_move_id',
        'user_id',
        'closed_by_id',
        'currency_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'state'                  => SessionState::class,
        'stock_update_mode'      => StockUpdateMode::class,
        'started_at'             => 'datetime',
        'stopped_at'             => 'datetime',
        'cash_balance_start'     => 'decimal:4',
        'cash_balance_end_real'  => 'decimal:4',
        'cash_balance_end'       => 'decimal:4',
        'cash_difference'        => 'decimal:4',
        'cash_transaction_total' => 'decimal:4',
        'total_payments_amount'  => 'decimal:4',
        'is_rescue'              => 'boolean',
        'has_failed_operations'  => 'boolean',
    ];

    protected $attributes = [
        'sequence_number'        => 0,
        'login_number'           => 0,
        'cash_balance_start'     => 0,
        'cash_transaction_total' => 0,
        'total_payments_amount'  => 0,
        'order_count'            => 0,
        'operation_count'        => 0,
        'is_rescue'              => false,
        'has_failed_operations'  => false,
    ];

    public function config(): BelongsTo
    {
        return $this->belongsTo(Config::class)->withTrashed();
    }

    public function rescueForSession(): BelongsTo
    {
        return $this->belongsTo(self::class, 'rescue_for_session_id');
    }

    public function rescueSessions(): HasMany
    {
        return $this->hasMany(self::class, 'rescue_for_session_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function deliveries(): HasManyThrough
    {
        return $this->hasManyThrough(
            Delivery::class,
            Order::class,
            'session_id',
            'origin',
            'id',
            'name',
        );
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class, 'session_id');
    }

    public function cashJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'cash_journal_id');
    }

    public function resolveCashJournal(): ?Journal
    {
        return $this->cashJournal
            ?? $this->config?->cashPaymentMethod()?->journal;
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class);
    }

    public function cogsMove(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'cogs_move_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function companyConsistentFields(): array
    {
        return [
            'config_id' => Config::class,
        ];
    }

    public function getModelTitle(): string
    {
        return __('point-of-sale::models/session.title');
    }

    public function getChatterResponsibles(): array
    {
        return ['user'];
    }

    public function isLive(): bool
    {
        return $this->state->isLive();
    }

    public function cashMovementTotal(): float
    {
        return (float) $this->cashMovements->sum(
            fn (CashMovement $cashMovement): float => $cashMovement->signedAmount()
        );
    }

    public function expectedCashBalance(): float
    {
        return (float) $this->cash_balance_start
            + (float) $this->cash_transaction_total
            + $this->cashMovementTotal();
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    public function computeUserId(): void
    {
        $this->user_id ??= Auth::id();
    }

    public function computeConfigDefaults(): void
    {
        $config = $this->config;

        if (! $config) {
            return;
        }

        $this->company_id ??= $config->company_id;

        $this->currency_id ??= $config->currency_id;

        $this->stock_update_mode ??= static::defaultStockUpdateMode();

        $this->cash_journal_id ??= $config->cashPaymentMethod()?->journal_id;
    }

    public static function defaultStockUpdateMode(): StockUpdateMode
    {
        try {
            return settings(InventorySettings::class)->stock_update_mode;
        } catch (Throwable) {
            return StockUpdateMode::REAL_TIME;
        }
    }

    public function computeState(): void
    {
        $this->state ??= SessionState::OPENING_CONTROL;
    }

    public function computeName(): void
    {
        if (filled($this->name) || ! Schema::hasTable('sequences')) {
            return;
        }

        $config = $this->config;

        if (! $config) {
            return;
        }

        $this->name = SequenceService::nextFor(
            $config,
            Config::SESSION_SEQUENCE_VARIANT,
            $this->company_id,
            $config->sequenceDefaults(Config::SESSION_SEQUENCE_VARIANT),
        );
    }

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'                  => __('point-of-sale::models/session.log-attributes.name'),
            'state'                 => __('point-of-sale::models/session.log-attributes.state'),
            'cash_balance_start'    => __('point-of-sale::models/session.log-attributes.cash-balance-start'),
            'cash_balance_end_real' => __('point-of-sale::models/session.log-attributes.cash-balance-end-real'),
            'cash_difference'       => __('point-of-sale::models/session.log-attributes.cash-difference'),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Session $session) {
            $session->computeCreatorId();

            $session->computeUserId();

            $session->computeConfigDefaults();

            $session->computeState();
        });

        static::created(function (Session $session) {
            $session->computeName();

            $session->saveQuietly();
        });
    }

    protected static function newFactory(): SessionFactory
    {
        return SessionFactory::new();
    }
}
