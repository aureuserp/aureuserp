<?php

namespace Webkul\Security\Models;

use App\Models\User as BaseUser;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Traits\HasPermissionScope;
use Webkul\Support\Models\Company;

class User extends BaseUser implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasEmailAuthentication
{
    use HasPermissionScope,
        HasRoles,
        InteractsWithAppAuthentication,
        InteractsWithAppAuthenticationRecovery,
        InteractsWithEmailAuthentication,
        SoftDeletes;

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'partner_id',
            'language',
            'creator_id',
            'is_active',
            'default_company_id',
            'resource_permission',
        ]);

        $this->mergeCasts([
            'default_company_id'  => 'integer',
            'resource_permission' => PermissionType::class,
        ]);

        parent::__construct($attributes);
    }

    protected function getAssignmentColumn(): ?string
    {
        return 'id';
    }

    protected $guard_name = ['web', 'sanctum'];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getAvatarUrlAttribute()
    {
        return $this->partner?->avatar_url;
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'user_team', 'user_id', 'team_id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function allowedCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_allowed_companies', 'user_id', 'company_id');
    }

    public function defaultCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'default_company_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->creator_id ??= Auth::id();
        });

        static::saved(function ($user) {
            if (! $user->partner_id) {
                $user->handlePartnerCreation($user);
            } else {
                $user->handlePartnerUpdation($user);
            }
        });
    }

    private function handlePartnerCreation(self $user)
    {
        $partner = $user->partner()->create([
            'creator_id' => Auth::user()->id ?? $user->id,
            'user_id'    => $user->id,
            'sub_type'   => 'partner',
            ...Arr::except($user->toArray(), ['id', 'partner_id']),
        ]);

        $user->partner_id = $partner->id;
        $user->save();
    }

    private function handlePartnerUpdation(self $user)
    {
        $partner = Partner::updateOrCreate(
            ['id' => $user->partner_id],
            [
                'creator_id' => Auth::user()->id ?? $user->id,
                'user_id'    => $user->id,
                'sub_type'   => 'partner',
                ...Arr::except($user->toArray(), ['id', 'partner_id']),
            ]
        );

        if ($user->partner_id !== $partner->id) {
            $user->partner_id = $partner->id;
            $user->save();
        }
    }
}
