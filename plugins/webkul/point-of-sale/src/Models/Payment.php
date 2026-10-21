<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;
use Webkul\PointOfSale\Database\Factories\PaymentFactory;
use Webkul\PointOfSale\Enums\TerminalPaymentStatus;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Payment extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'pos_payments';

    protected $fillable = [
        'uuid',
        'amount',
        'currency_rate',
        'terminal_status',
        'transaction_reference',
        'card_type',
        'card_brand',
        'cardholder_name',
        'ticket',
        'paid_at',
        'is_change',
        'order_id',
        'session_id',
        'payment_method_id',
        'partner_id',
        'account_move_id',
        'user_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'terminal_status' => TerminalPaymentStatus::class,
        'amount'          => 'decimal:4',
        'currency_rate'   => 'decimal:4',
        'paid_at'         => 'datetime',
        'is_change'       => 'boolean',
    ];

    protected $attributes = [
        'amount'        => 0,
        'currency_rate' => 1,
        'is_change'     => false,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class)->withTrashed();
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function accountMove(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'account_move_id');
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

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    public function computeUuid(): void
    {
        $this->uuid ??= Str::uuid()->toString();
    }

    public function computePaidAt(): void
    {
        $this->paid_at ??= now();
    }

    public function inheritFromOrder(): void
    {
        $order = $this->order;

        if (! $order) {
            return;
        }

        $this->session_id ??= $order->session_id;

        $this->company_id ??= $order->company_id;

        $this->partner_id ??= $order->partner_id;

        $this->user_id ??= $order->user_id;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Payment $payment) {
            $payment->computeCreatorId();

            $payment->computeUuid();

            $payment->computePaidAt();

            $payment->inheritFromOrder();
        });
    }

    protected static function newFactory(): PaymentFactory
    {
        return PaymentFactory::new();
    }
}
