<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Crm\Enums\RevealViewState;

class RevealView extends Model
{
    protected $table = 'crm_reveal_views';

    protected $fillable = [
        'reveal_ip',
        'visitor_id',
        'url',
        'reveal_state',
        'reveal_rule_id',
    ];

    protected $casts = [
        'reveal_state' => RevealViewState::class,
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(RevealRule::class, 'reveal_rule_id');
    }
}
