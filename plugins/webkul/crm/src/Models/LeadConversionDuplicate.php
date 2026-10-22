<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadConversionDuplicate extends Model
{
    public $timestamps = false;

    protected $table = 'crm_lead_conversion_duplicates';

    protected $fillable = [
        'conversion_id',
        'lead_id',
        'original_lead_id',
        'lead_name',
    ];

    public function conversion(): BelongsTo
    {
        return $this->belongsTo(LeadConversion::class, 'conversion_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id')->withTrashed();
    }
}
