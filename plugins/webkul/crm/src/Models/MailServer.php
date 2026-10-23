<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class MailServer extends Model
{
    public const ENCRYPTION_SSL = 'ssl';

    public const ENCRYPTION_TLS = 'tls';

    public const ENCRYPTION_NONE = 'none';

    protected $table = 'crm_mail_servers';

    protected $fillable = [
        'name',
        'host',
        'port',
        'encryption',
        'validate_certificate',
        'username',
        'password',
        'folder',
        'is_active',
        'last_fetched_at',
        'last_error',
        'company_id',
        'creator_id',
    ];

    protected $attributes = [
        'port'                 => 993,
        'encryption'           => self::ENCRYPTION_SSL,
        'validate_certificate' => true,
        'folder'               => 'INBOX',
        'is_active'            => true,
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'port'                 => 'integer',
        'validate_certificate' => 'boolean',
        'password'             => 'encrypted',
        'is_active'            => 'boolean',
        'last_fetched_at'      => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function inboundEmails(): HasMany
    {
        return $this->hasMany(InboundEmail::class, 'mail_server_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected static function booted(): void
    {
        static::creating(function (self $server): void {
            $server->creator_id ??= Auth::id();
        });
    }
}
