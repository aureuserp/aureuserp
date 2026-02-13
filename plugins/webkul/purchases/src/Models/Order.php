<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Incoterm;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Chatter\Models\Message;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Purchase\Database\Factories\OrderFactory;
use Webkul\Purchase\Enums\OrderInvoiceStatus;
use Webkul\Purchase\Enums\OrderReceiptStatus;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasPermissionScope;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class Order extends Model
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, HasPermissionScope;

    protected $table = 'purchases_orders';

    protected $fillable = [
        'name',
        'description',
        'priority',
        'origin',
        'partner_reference',
        'state',
        'invoice_status',
        'receipt_status',
        'untaxed_amount',
        'tax_amount',
        'total_amount',
        'total_cc_amount',
        'currency_rate',
        'mail_reminder_confirmed',
        'mail_reception_confirmed',
        'mail_reception_declined',
        'invoice_count',
        'ordered_at',
        'approved_at',
        'planned_at',
        'calendar_start_at',
        'incoterm_location',
        'effective_date',
        'report_grids',
        'requisition_id',
        'purchases_group_id',
        'partner_id',
        'currency_id',
        'fiscal_position_id',
        'payment_term_id',
        'incoterm_id',
        'user_id',
        'company_id',
        'creator_id',
        'operation_type_id',
    ];

    protected $casts = [
        'state'                    => OrderState::class,
        'invoice_status'           => OrderInvoiceStatus::class,
        'receipt_status'           => OrderReceiptStatus::class,
        'mail_reminder_confirmed'  => 'boolean',
        'mail_reception_confirmed' => 'boolean',
        'mail_reception_declined'  => 'boolean',
        'report_grids'             => 'boolean',
        'ordered_at'               => 'datetime',
        'approved_at'              => 'datetime',
        'planned_at'               => 'datetime',
        'calendar_start_at'        => 'datetime',
        'effective_date'           => 'datetime',
        'untaxed_amount'           => 'decimal:4',
    ];

    public function getModelTitle(): string
    {
        return __('purchases::models/order.title');
    }

    public function getLogAttributeLabels(): array
    {
        return [
            'state'             => __('purchases::models/order.log-attributes.state'),
            'untaxed_amount'    => __('purchases::models/order.log-attributes.untaxed-amount'),
            'partner_reference' => __('purchases::models/order.log-attributes.partner-reference'),
            'origin'            => __('purchases::models/order.log-attributes.origin'),
            'partner.name'      => __('purchases::models/order.log-attributes.partner'),
            'user.name'         => __('purchases::models/order.log-attributes.buyer'),
            'paymentTerm.name'  => __('purchases::models/order.log-attributes.payment-term'),
            'fiscalPosition'    => __('purchases::models/order.log-attributes.fiscal-position'),
        ];
    }

    public function getQtyToInvoiceAttribute()
    {
        return $this->lines->sum('qty_to_invoice');
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(Requisition::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(OrderGroup::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function fiscalPosition(): BelongsTo
    {
        return $this->belongsTo(FiscalPosition::class);
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function incoterm(): BelongsTo
    {
        return $this->belongsTo(Incoterm::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
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
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class, 'order_id');
    }

    public function accountMoves(): BelongsToMany
    {
        return $this->belongsToMany(AccountMove::class, 'purchases_order_account_moves', 'order_id', 'move_id');
    }

    public function operationType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'operation_type_id');
    }

    public function operations(): BelongsToMany
    {
        return $this->belongsToMany(Operation::class, 'purchases_order_operations', 'purchase_order_id', 'inventory_operation_id');
    }

    public function addMessage(array $data): Message
    {
        $message = new Message;

        $user = Auth::user();

        $message->fill(array_merge([
            'creator_id'       => $user?->id,
            'date_deadline'    => $data['date_deadline'] ?? now(),
            'company_id'       => $data['company_id'] ?? ($user->defaultCompany?->id ?? null),
            'messageable_type' => Order::class,
            'messageable_id'   => $this->id,
        ], $data));

        $message->save();

        return $message;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->creator_id ??= Auth::id();

            $order->calendar_start_at ??= $order->ordered_at;

            $order->state ??= OrderState::DRAFT;
        });

        static::saving(function ($order) {
            $order->updateName();
        });

        static::created(function ($order) {
            $order->update(['name' => $order->name]);
        });
    }

    public function updateName()
    {
        $this->name = 'PO/'.$this->id;
    }

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}
