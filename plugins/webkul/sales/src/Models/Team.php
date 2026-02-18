<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Sale\Database\Factories\TeamFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Team extends Model implements Sortable
{
    use HasChatter, HasFactory, HasLogActivity, SoftDeletes, SortableTrait;

    protected $table = 'sales_teams';

    public function getModelTitle(): string
    {
        return __('sales::models/team.title');
    }

    protected $fillable = [
        'sort',
        'company_id',
        'user_id',
        'color',
        'creator_id',
        'name',
        'is_active',
        'invoiced_target',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'name'               => __('sales::models/team.log-attributes.name'),
            'company.name'       => __('sales::models/team.log-attributes.company'),
            'user.name'          => __('sales::models/team.log-attributes.team_leader'),
            'creator.name'       => __('sales::models/team.log-attributes.creator'),
            'is_active'          => __('sales::models/team.log-attributes.status'),
            'invoiced_target'    => __('sales::models/team.log-attributes.invoiced_target'),
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'sales_team_members', 'team_id', 'user_id');
    }

    protected static function newFactory(): TeamFactory
    {
        return TeamFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            $team->creator_id ??= Auth::id();
        });
    }
}
