<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Partner\Models\Partner;

class CrmPartner extends Model
{
    protected $table = 'crm_partners';

    protected $fillable = [
        'partner_id',
        'level_id',
        'level_weight',
    ];

    protected $casts = [
        'level_weight' => 'integer',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(PartnerLevel::class, 'level_id')->withTrashed();
    }

    public function effectiveWeight(): int
    {
        return $this->level_weight ?? (int) ($this->level?->partner_weight ?? 0);
    }
}
