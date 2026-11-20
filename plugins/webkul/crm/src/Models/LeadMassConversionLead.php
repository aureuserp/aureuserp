<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadMassConversionLead extends Model
{
    public $timestamps = false;

    protected $table = 'crm_lead_mass_conversion_leads';

    protected $fillable = [
        'mass_conversion_id',
        'lead_id',
        'original_lead_id',
        'lead_name',
        'opportunity_id',
    ];

    public function massConversion(): BelongsTo
    {
        return $this->belongsTo(LeadMassConversion::class, 'mass_conversion_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id')->withTrashed();
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'opportunity_id')->withTrashed();
    }
}
