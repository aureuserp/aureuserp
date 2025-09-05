<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Partner\Models\Partner;
use Webkul\Purchase\Database\Factories\RequisitionFactory;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class Requisition extends Model
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, SoftDeletes;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'purchases_requisitions';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'state',
        'reference',
        'starts_at',
        'ends_at',
        'description',
        'currency_id',
        'partner_id',
        'user_id',
        'company_id',
        'creator_id',
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected $casts = [
        'state' => RequisitionState::class,
        'type'  => RequisitionType::class,
    ];

    protected array $logAttributes = [
        'name',
        'type',
        'state',
        'reference',
        'starts_at',
        'ends_at',
        'description',
        'currency.name' => 'Currency',
        'partner.name'  => 'Partner',
        'user.name'     => 'Buyer',
        'company.name'  => 'Company',
        'creator.name'  => 'Creator',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
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
        return $this->hasMany(RequisitionLine::class);
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
        if ($this->type == RequisitionType::BLANKET_ORDER) {
            $this->name = 'BO/'.$this->id;
        } else {
            $this->name = 'PT/'.$this->id;
        }
    }

    protected static function newFactory(): RequisitionFactory
    {
        return RequisitionFactory::new();
    }
}
