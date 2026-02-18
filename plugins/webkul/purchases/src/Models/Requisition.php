<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
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

    protected $table = 'purchases_requisitions';

    public function getModelTitle(): string
    {
        return __('purchases::models/requisition.title');
    }

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

    protected $casts = [
        'state' => RequisitionState::class,
        'type'  => RequisitionType::class,
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'state'        => trans('purchases::models/requisition.log-attributes.state'),
            'reference'    => trans('purchases::models/requisition.log-attributes.reference'),
            'starts_at'    => trans('purchases::models/requisition.log-attributes.starts-at'),
            'ends_at'      => trans('purchases::models/requisition.log-attributes.ends-at'),
            'partner.name' => trans('purchases::models/requisition.log-attributes.partner'),
            'user.name'    => trans('purchases::models/requisition.log-attributes.buyer'),
        ];
    }

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($requisition) {
            $requisition->creator_id ??= Auth::id();

            $requisition->state ??= RequisitionState::DRAFT;
        });

        static::saving(function ($requisition) {
            $requisition->updateName();
        });

        static::created(function ($requisition) {
            $requisition->update(['name' => $requisition->name]);
        });
    }
}
