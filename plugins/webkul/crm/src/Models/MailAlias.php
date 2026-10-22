<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Sale\Models\Team;

class MailAlias extends Model
{
    public const CONTACT_EVERYONE = 'everyone';

    public const CONTACT_PARTNERS = 'partners';

    public const CONTACT_FOLLOWERS = 'followers';

    public const CONTACT_EMPLOYEES = 'employees';

    public const CONTACTS = [
        self::CONTACT_EVERYONE,
        self::CONTACT_PARTNERS,
        self::CONTACT_FOLLOWERS,
        self::CONTACT_EMPLOYEES,
    ];

    protected $table = 'crm_mail_aliases';

    protected $fillable = [
        'team_id',
        'alias_name',
        'alias_domain',
        'alias_contact',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id')->withTrashed();
    }

    public function getEmailAttribute(): ?string
    {
        if (blank($this->alias_name)) {
            return null;
        }

        return filled($this->alias_domain) ? $this->alias_name.'@'.$this->alias_domain : $this->alias_name;
    }

    public static function normalizeName(?string $name): ?string
    {
        $name = mb_strtolower(trim((string) $name));

        return $name === '' ? null : $name;
    }

    public static function normalizeDomain(?string $domain): ?string
    {
        $domain = mb_strtolower(trim((string) $domain, " \t@"));

        return $domain === '' ? null : $domain;
    }

    protected static function booted(): void
    {
        static::saving(function (self $alias): void {
            $alias->alias_name = static::normalizeName($alias->alias_name);

            $alias->alias_domain = static::normalizeDomain($alias->alias_domain);

            $alias->alias_contact = in_array($alias->alias_contact, self::CONTACTS, true) ? $alias->alias_contact : self::CONTACT_EVERYONE;
        });
    }
}
