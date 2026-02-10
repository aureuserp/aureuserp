<?php

namespace Webkul\Partner\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Partner\Database\Factories\PartnerFactory;
use Webkul\Partner\Enums\AccountType;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasPermissionScope;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

class Partner extends Authenticatable implements FilamentUser
{
    use HasChatter, HasFactory, HasLogActivity, HasPermissionScope, Notifiable, SoftDeletes;

    protected $table = 'partners_partners';

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

    public function getModelTitle(): string
    {
        return 'Partner';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    protected function getOwnerColumn(): string
    {
        return 'creator_id';
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
}
