<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class Order extends Model
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity;

    public function getModelTitle(): string
    {
        return __('purchases::models/order.title');
    }

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'purchases_orders';

    /**
     * Fillable.
     *
     * @var array
     */
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

    /**
     * Table name.
     *
     * @var string
     */
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

    public function getLogAttributeLabels(): array
    {
        return [
            'name'               => __('purchases::models/order.log-attributes.name'),
            'description'        => __('purchases::models/order.log-attributes.description'),
            'priority'           => __('purchases::models/order.log-attributes.priority'),
            'origin'             => __('purchases::models/order.log-attributes.origin'),
            'partner_reference'  => __('purchases::models/order.log-attributes.partner_reference'),
            'state'              => __('purchases::models/order.log-attributes.state'),
            'invoice_status'     => __('purchases::models/order.log-attributes.invoice_status'),
            'receipt_status'     => __('purchases::models/order.log-attributes.receipt_status'),
            'untaxed_amount'     => __('purchases::models/order.log-attributes.untaxed_amount'),
            'currency_rate'      => __('purchases::models/order.log-attributes.currency_rate'),
            'ordered_at'         => __('purchases::models/order.log-attributes.ordered_at'),
            'approved_at'        => __('purchases::models/order.log-attributes.approved_at'),
            'planned_at'         => __('purchases::models/order.log-attributes.planned_at'),
            'calendar_start_at'  => __('purchases::models/order.log-attributes.calendar_start_at'),
            'incoterm_location'  => __('purchases::models/order.log-attributes.incoterm_location'),
            'effective_date'     => __('purchases::models/order.log-attributes.effective_date'),
            'requisition.name'   => __('purchases::models/order.log-attributes.requisition'),
            'partner.name'       => __('purchases::models/order.log-attributes.vendor'),
            'currency.name'      => __('purchases::models/order.log-attributes.currency'),
            'fiscalPosition'     => __('purchases::models/order.log-attributes.fiscal_position'),
            'paymentTerm.name'   => __('purchases::models/order.log-attributes.payment_term'),
            'incoterm.name'      => __('purchases::models/order.log-attributes.incoterm'),
            'user.name'          => __('purchases::models/order.log-attributes.buyer'),
            'company.name'       => __('purchases::models/order.log-attributes.company'),
            'creator.name'       => __('purchases::models/order.log-attributes.creator'),
        ];
    }

    /**
     * Checks if new invoice is allow or not
     */
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

    /**
     * Add a new message
     */
    public function addMessage(array $data): Message
    {
        $message = new Message;

        $user = filament()->auth()->user();

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

    /**
     * Bootstrap any application services.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            $order->updateName();
        });

        static::created(function ($order) {
            $order->update(['name' => $order->name]);
        });
    }

    /**
     * Update the full name without triggering additional events
     */
    public function updateName()
    {
        $this->name = 'PO/'.$this->id;
    }

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}
