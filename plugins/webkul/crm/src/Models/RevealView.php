<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevealView extends Model
{
    public const STATE_TO_PROCESS = 'to_process';

    protected $table = 'crm_reveal_views';

    protected $fillable = [
        'reveal_ip',
        'visitor_id',
        'url',
        'reveal_state',
        'reveal_rule_id',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(RevealRule::class, 'reveal_rule_id');
    }
}
