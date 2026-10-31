<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Enums\QuotationPartnerAction;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Quotation;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class QuotationPartner extends Model
{
    protected $table = 'crm_quotation_partners';

    protected $fillable = [
        'opportunity_id',
        'action',
        'partner_id',
        'quotation_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'action' => QuotationPartnerAction::class,
    ];

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'opportunity_id')->withTrashed();
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $record): void {
            $record->creator_id ??= Auth::id();
        });
    }
}
