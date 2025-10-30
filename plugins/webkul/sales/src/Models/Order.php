<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Models\UTMMedium;
use Webkul\Support\Models\UTMSource;

class Order extends Model
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'sales_orders';

    protected $fillable = [
        'utm_source_id',
        'medium_id',
        'company_id',
        'partner_id',
        'journal_id',
        'partner_invoice_id',
        'partner_shipping_id',
        'fiscal_position_id',
        'sale_order_template_id',
        'payment_term_id',
        'currency_id',
        'user_id',
        'team_id',
        'creator_id',
        'campaign_id',
        'access_token',
        'name',
        'state',
        'client_order_ref',
        'origin',
        'reference',
        'signed_by',
        'invoice_status',
        'validity_date',
        'note',
        'locked',
        'commitment_date',
        'date_order',
        'signed_on',
        'prepayment_percent',
        'require_signature',
        'require_payment',
        'currency_rate',
        'amount_untaxed',
        'amount_tax',
        'amount_total',
        'warehouse_id',
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'medium.name'          => trans('sales::models/order.log-attributes.medium'),
            'utmSource.name'       => trans('sales::models/order.log-attributes.utm_source'),
            'partner.name'         => trans('sales::models/order.log-attributes.customer'),
            'partnerInvoice.name'  => trans('sales::models/order.log-attributes.invoice_address'),
            'partnerShipping.name' => trans('sales::models/order.log-attributes.shipping_address'),
            'fiscalPosition.name'  => trans('sales::models/order.log-attributes.fiscal_position'),
            'paymentTerm.name'     => trans('sales::models/order.log-attributes.payment_term'),
            'currency.name'        => trans('sales::models/order.log-attributes.currency'),
            'user.name'            => trans('sales::models/order.log-attributes.salesperson'),
            'team.name'            => trans('sales::models/order.log-attributes.sales_team'),
            'creator.name'         => trans('sales::models/order.log-attributes.creator'),
            'company.name'         => trans('sales::models/order.log-attributes.company'),
            'name'                 => trans('sales::models/order.log-attributes.order_reference'),
            'state'                => trans('sales::models/order.log-attributes.order_status'),
            'client_order_ref'     => trans('sales::models/order.log-attributes.customer_reference'),
            'origin'               => trans('sales::models/order.log-attributes.source_document'),
            'reference'            => trans('sales::models/order.log-attributes.reference'),
            'signed_by'            => trans('sales::models/order.log-attributes.signed_by'),
            'invoice_status'       => trans('sales::models/order.log-attributes.invoice_status'),
            'validity_date'        => trans('sales::models/order.log-attributes.validity_date'),
            'note'                 => trans('sales::models/order.log-attributes.note'),
            'currency_rate'        => trans('sales::models/order.log-attributes.currency_rate'),
            'amount_untaxed'       => trans('sales::models/order.log-attributes.subtotal'),
            'amount_tax'           => trans('sales::models/order.log-attributes.tax'),
            'amount_total'         => trans('sales::models/order.log-attributes.total'),
            'locked'               => trans('sales::models/order.log-attributes.locked'),
            'require_signature'    => trans('sales::models/order.log-attributes.require_signature'),
            'require_payment'      => trans('sales::models/order.log-attributes.require_payment'),
            'commitment_date'      => trans('sales::models/order.log-attributes.commitment_date'),
            'date_order'           => trans('sales::models/order.log-attributes.order_date'),
            'signed_on'            => trans('sales::models/order.log-attributes.signed_on'),
            'prepayment_percent'   => trans('sales::models/order.log-attributes.prepayment_percent'),
        ];
    }

    protected $casts = [
        'amount_tax'     => 'decimal:4',
        'amount_total'   => 'decimal:4',
        'amount_untaxed' => 'decimal:4',
        'state'          => OrderState::class,
        'invoice_status' => InvoiceStatus::class,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function getQtyToInvoiceAttribute()
    {
        return $this->lines->sum('qty_to_invoice');
    }

    public function campaign()
    {
        return $this->belongsTo(UtmCampaign::class, 'campaign_id');
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function accountMoves(): BelongsToMany
    {
        return $this->belongsToMany(Move::class, 'sales_order_invoices', 'order_id', 'move_id');
    }

    public function partnerInvoice()
    {
        return $this->belongsTo(Partner::class, 'partner_invoice_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'sales_order_tags', 'order_id', 'tag_id');
    }

    public function partnerShipping()
    {
        return $this->belongsTo(Partner::class, 'partner_shipping_id');
    }

    public function fiscalPosition()
    {
        return $this->belongsTo(FiscalPosition::class);
    }

    public function paymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function utmSource()
    {
        return $this->belongsTo(UTMSource::class, 'utm_source_id');
    }

    public function medium()
    {
        return $this->belongsTo(UTMMedium::class);
    }

    public function lines()
    {
        return $this->hasMany(OrderLine::class);
    }

    public function optionalLines()
    {
        return $this->hasMany(OrderOption::class);
    }

    public function quotationTemplate()
    {
        return $this->belongsTo(OrderTemplate::class, 'sale_order_template_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class, 'sale_order_id');
    }

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
     * Update the name based on the state without trigger any additional events.
     */
    public function updateName()
    {
        $this->name = 'SO/'.$this->id;
    }
}
