<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Move;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\ProcurementGroup;
use Webkul\Partner\Models\Partner;
use Webkul\PointOfSale\Database\Factories\OrderFactory;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\Product\Models\PriceList;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class Order extends Model
{
    use BelongsToCompany, ChecksCompanyConsistency, HasChatter, HasCustomFields, HasFactory, HasLogActivity;

    protected $table = 'pos_orders';

    protected $fillable = [
        'uuid',
        'name',
        'reference',
        'tracking_number',
        'receipt_code',
        'access_token',
        'origin',
        'state',
        'sequence_number',
        'ordered_at',
        'confirmed_at',
        'shipped_at',
        'currency_rate',
        'amount_untaxed',
        'amount_tax',
        'amount_total',
        'amount_paid',
        'amount_return',
        'amount_difference',
        'total_cost',
        'margin',
        'margin_percent',
        'tip_amount',
        'amount_rounding',
        'print_count',
        'customer_count',
        'note',
        'email',
        'mobile',
        'is_to_invoice',
        'is_invoiced',
        'is_tipped',
        'is_takeaway',
        'is_booked',
        'preparation_state',
        'floating_name',
        'is_cost_computed',
        'is_edited',
        'has_failed_operation',
        'session_id',
        'config_id',
        'partner_id',
        'price_list_id',
        'fiscal_position_id',
        'currency_id',
        'operation_id',
        'procurement_group_id',
        'refunded_order_id',
        'account_move_id',
        'table_id',
        'user_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'state'                => OrderState::class,
        'ordered_at'           => 'datetime',
        'confirmed_at'         => 'datetime',
        'shipped_at'           => 'date',
        'currency_rate'        => 'decimal:4',
        'amount_untaxed'       => 'decimal:4',
        'amount_tax'           => 'decimal:4',
        'amount_total'         => 'decimal:4',
        'amount_paid'          => 'decimal:4',
        'amount_return'        => 'decimal:4',
        'amount_difference'    => 'decimal:4',
        'total_cost'           => 'decimal:4',
        'margin'               => 'decimal:4',
        'margin_percent'       => 'decimal:4',
        'tip_amount'           => 'decimal:4',
        'amount_rounding'      => 'decimal:4',
        'is_to_invoice'        => 'boolean',
        'is_invoiced'          => 'boolean',
        'is_tipped'            => 'boolean',
        'is_takeaway'          => 'boolean',
        'is_booked'            => 'boolean',
        'preparation_state'    => 'array',
        'is_cost_computed'     => 'boolean',
        'is_edited'            => 'boolean',
        'has_failed_operation' => 'boolean',
    ];

    protected $attributes = [
        'state'                => 'draft',
        'sequence_number'      => 0,
        'currency_rate'        => 1,
        'amount_untaxed'       => 0,
        'amount_tax'           => 0,
        'amount_total'         => 0,
        'amount_paid'          => 0,
        'amount_return'        => 0,
        'amount_difference'    => 0,
        'total_cost'           => 0,
        'margin'               => 0,
        'margin_percent'       => 0,
        'tip_amount'           => 0,
        'amount_rounding'      => 0,
        'print_count'          => 0,
        'customer_count'       => 0,
        'is_to_invoice'        => false,
        'is_invoiced'          => false,
        'is_tipped'            => false,
        'is_takeaway'          => false,
        'is_booked'            => false,
        'is_cost_computed'     => false,
        'is_edited'            => false,
        'has_failed_operation' => false,
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function config(): BelongsTo
    {
        return $this->belongsTo(Config::class)->withTrashed();
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class, 'order_id')->orderBy('sort');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function fiscalPosition(): BelongsTo
    {
        return $this->belongsTo(FiscalPosition::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function procurementGroup(): BelongsTo
    {
        return $this->belongsTo(ProcurementGroup::class);
    }

    public function refundedOrder(): BelongsTo
    {
        return $this->belongsTo(self::class, 'refunded_order_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(self::class, 'refunded_order_id');
    }

    public function accountMove(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'account_move_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'invoice_origin', 'name');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class, 'origin', 'name');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'origin', 'name');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
            'session_id' => Session::class,
            'config_id'  => Config::class,
        ];
    }

    public function getModelTitle(): string
    {
        return __('point-of-sale::models/order.title');
    }

    public function getChatterResponsibles(): array
    {
        return ['user'];
    }

    public function scopeDrafts(Builder $query): Builder
    {
        return $query->where('state', OrderState::DRAFT);
    }

    public function scopeForSession(Builder $query, int $sessionId): Builder
    {
        return $query->where('session_id', $sessionId);
    }

    public function isRefund(): bool
    {
        return $this->refunded_order_id !== null;
    }

    public function paidAmount(): float
    {
        return (float) $this->payments()->where('is_change', false)->sum('amount');
    }

    public function settledAmount(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    public function computeUuid(): void
    {
        $this->uuid ??= Str::uuid()->toString();
    }

    public function computeAccessToken(): void
    {
        $this->access_token ??= Str::uuid()->toString();
    }

    public function computeOrderedAt(): void
    {
        $this->ordered_at ??= now();
    }

    public function computeSessionDefaults(): void
    {
        $session = $this->session;

        if (! $session) {
            return;
        }

        $this->config_id ??= $session->config_id;

        $this->company_id ??= $session->company_id;

        $this->currency_id ??= $session->currency_id;

        $this->user_id ??= $session->user_id;
    }

    public function computeSequenceNumber(): void
    {
        if ($this->sequence_number) {
            return;
        }

        $this->sequence_number = (int) static::withoutGlobalScopes()
            ->where('session_id', $this->session_id)
            ->max('sequence_number') + 1;
    }

    public function computeReference(): void
    {
        if (filled($this->reference) || ! $this->session_id) {
            return;
        }

        $this->reference = sprintf(
            '%05d-%03d-%04d',
            $this->session_id,
            $this->session?->login_number ?? 0,
            $this->sequence_number,
        );
    }

    public function computeTrackingNumber(): void
    {
        if (filled($this->tracking_number) || ! $this->session_id) {
            return;
        }

        $this->tracking_number = str_pad(
            (string) ((($this->session_id % 10) * 100) + ($this->sequence_number % 100)),
            3,
            '0',
            STR_PAD_LEFT,
        );
    }

    public function computeReceiptCode(): void
    {
        $this->receipt_code ??= Str::upper(Str::random(5));
    }

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'         => __('point-of-sale::models/order.log-attributes.name'),
            'state'        => __('point-of-sale::models/order.log-attributes.state'),
            'amount_total' => __('point-of-sale::models/order.log-attributes.amount-total'),
            'amount_paid'  => __('point-of-sale::models/order.log-attributes.amount-paid'),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Order $order) {
            $order->computeCreatorId();

            $order->computeUuid();

            $order->computeAccessToken();

            $order->computeOrderedAt();

            $order->computeSessionDefaults();

            $order->computeSequenceNumber();

            $order->computeReference();

            $order->computeTrackingNumber();

            $order->computeReceiptCode();
        });
    }

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}
