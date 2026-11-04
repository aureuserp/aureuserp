<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class LeadMerge extends Model
{
    protected $table = 'crm_lead_merges';

    protected $fillable = [
        'lead_id',
        'merged_records',
        'merged_by',
        'company_id',
    ];

    protected $casts = [
        'merged_records' => 'array',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id')->withTrashed();
    }

    public function mergedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merged_by');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
