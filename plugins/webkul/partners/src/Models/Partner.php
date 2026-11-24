<?php

namespace Webkul\Partner\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Partner\Database\Factories\PartnerFactory;
use Webkul\Partner\Enums\AccountType;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasOwnershipScope;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Concerns\HasContributedAttributes;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;
use Webkul\Support\Traits\BelongsToCompany;

class Partner extends Authenticatable implements FilamentUser
{
    use BelongsToCompany;
    use HasChatter,HasContributedAttributes, HasCustomFields, HasFactory, HasLogActivity, HasOwnershipScope, Notifiable, SoftDeletes;

    public const ACTIVITY_PLAN_PLUGIN = 'partners';

    protected $table = 'partners_partners';

    protected static function ownershipScopeIsGlobal(): bool
    {
        return false;
    }

    protected $fillable = [
        'account_type',
        'sub_type',
        'name',
        'avatar',
        'email',
        'job_title',
        'website',
        'tax_id',
        'phone',
        'mobile',
        'color',
        'company_registry',
        'reference',
        'street1',
        'street2',
        'city',
        'zip',
        'state_id',
        'country_id',
        'parent_id',
        'creator_id',
        'user_id',
        'title_id',
        'company_id',
        'industry_id',
    ];

    protected $casts = [
        'account_type' => AccountType::class,
        'is_active'    => 'boolean',
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'name'         => __('partners::models/partner.log-attributes.name'),
            'email'        => __('partners::models/partner.log-attributes.email'),
            'phone'        => __('partners::models/partner.log-attributes.phone'),
            'parent.name'  => __('partners::models/partner.log-attributes.parent_company'),
            'user.name'    => __('partners::models/partner.log-attributes.user'),
            'tax_id'       => __('partners::models/partner.log-attributes.tax_id'),
            'street1'      => __('partners::models/partner.log-attributes.street1'),
            'street2'      => __('partners::models/partner.log-attributes.street2'),
            'city'         => __('partners::models/partner.log-attributes.city'),
            'zip'          => __('partners::models/partner.log-attributes.zip'),
            'state.name'   => __('partners::models/partner.log-attributes.state'),
            'country.name' => __('partners::models/partner.log-attributes.country'),
        ];
    }

    public function getModelTitle(): string
    {
        return 'Partner';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getAvatarUrlAttribute()
    {
        if (! $this->avatar) {
            return;
        }

        return Storage::url($this->avatar);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('account_type', AccountType::ADDRESS);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('account_type', '!=', AccountType::ADDRESS);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'partner_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'partners_partner_tag', 'partner_id', 'tag_id');
    }

    protected static function newFactory(): PartnerFactory
    {
        return PartnerFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($partner) {
            $partner->creator_id ??= Auth::id();
        });
    }

    public static function autoAssignsCompany(): bool
    {
        return false;
    }
}
