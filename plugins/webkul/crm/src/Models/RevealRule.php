<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Enums\RevealRuleContactCount;
use Webkul\Crm\Enums\RevealRuleContactFilter;
use Webkul\Crm\Enums\RevealRuleLeadFor;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

class RevealRule extends Model
{
    protected $table = 'crm_reveal_rules';

    protected $fillable = [
        'name',
        'is_active',
        'sort',
        'regex_url',
        'lead_for',
        'lead_type',
        'suffix',
        'priority',
        'filter_on_size',
        'company_size_min',
        'company_size_max',
        'contact_filter_type',
        'preferred_role_id',
        'seniority_id',
        'extra_contacts',
        'team_id',
        'user_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'sort'                => 'integer',
        'lead_for'            => RevealRuleLeadFor::class,
        'lead_type'           => LeadType::class,
        'contact_filter_type' => RevealRuleContactFilter::class,
        'priority'            => OpportunityPriority::class,
        'filter_on_size'      => 'boolean',
        'company_size_min'    => 'integer',
        'company_size_max'    => 'integer',
        'extra_contacts'      => RevealRuleContactCount::class,
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'crm_reveal_rule_countries', 'rule_id', 'country_id');
    }

    public function states(): BelongsToMany
    {
        return $this->belongsToMany(State::class, 'crm_reveal_rule_states', 'rule_id', 'state_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'crm_reveal_rule_tags', 'rule_id', 'tag_id');
    }

    public function industries(): BelongsToMany
    {
        return $this->belongsToMany(LeadIndustry::class, 'crm_reveal_rule_industries', 'rule_id', 'industry_id');
    }

    public function preferredRole(): BelongsTo
    {
        return $this->belongsTo(LeadRole::class, 'preferred_role_id');
    }

    public function otherRoles(): BelongsToMany
    {
        return $this->belongsToMany(LeadRole::class, 'crm_reveal_rule_roles', 'rule_id', 'role_id');
    }

    public function seniority(): BelongsTo
    {
        return $this->belongsTo(LeadSeniority::class, 'seniority_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'reveal_rule_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    public static function isValidPattern(string $pattern): bool
    {
        return @preg_match(static::delimit($pattern), '') !== false;
    }

    protected static function delimit(string $pattern): string
    {
        return '#'.str_replace('#', '\#', $pattern === '/' ? '.*/$' : $pattern).'#i';
    }

    public function matchesUrl(?string $url): bool
    {
        if (blank($this->regex_url)) {
            return true;
        }

        return @preg_match(static::delimit($this->regex_url), static::relativePath($url)) === 1;
    }

    public static function relativePath(?string $url): string
    {
        $path = rtrim((string) parse_url((string) $url, PHP_URL_PATH), '/');
        $base = rtrim((string) parse_url(url('/'), PHP_URL_PATH), '/');

        if ($base !== ''
            && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        return $path === '' ? '/' : $path;
    }

    public function matchesLocation(?int $countryId, ?int $stateId = null): bool
    {
        $countryIds = $this->countries->modelKeys();
        $stateIds = $this->states->modelKeys();

        return ($countryIds === [] || in_array($countryId, $countryIds, true))
            && ($stateIds === [] || in_array($stateId, $stateIds, true));
    }

    protected static function booted(): void
    {
        static::creating(function (self $rule): void {
            $rule->creator_id ??= Auth::id();
        });
    }
}
