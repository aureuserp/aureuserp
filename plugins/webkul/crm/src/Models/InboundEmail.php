<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundEmail extends Model
{
    public const STATUS_LEAD_CREATED = 'lead_created';

    public const STATUS_REPLY_ADDED = 'reply_added';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_IGNORED = 'ignored';

    public const STATUS_FAILED = 'failed';

    public const STATUSES = [
        self::STATUS_LEAD_CREATED,
        self::STATUS_REPLY_ADDED,
        self::STATUS_REJECTED,
        self::STATUS_IGNORED,
        self::STATUS_FAILED,
    ];

    protected $table = 'crm_inbound_emails';

    protected $fillable = [
        'mail_server_id',
        'alias_id',
        'lead_id',
        'message_id',
        'in_reply_to',
        'from_email',
        'from_name',
        'recipients',
        'subject',
        'status',
        'reason',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function mailServer(): BelongsTo
    {
        return $this->belongsTo(MailServer::class, 'mail_server_id');
    }

    public function alias(): BelongsTo
    {
        return $this->belongsTo(MailAlias::class, 'alias_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id')->withTrashed();
    }
}
