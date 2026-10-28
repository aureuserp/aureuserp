<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Crm\Database\Factories\StageFactory;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

class Stage extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'crm_stages';

    protected $fillable = [
        'sort',
        'name',
        'is_won',
        'requirements',
        'creator_id',
    ];

    protected $casts = [
        'is_won' => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'crm_stage_team_rel', 'stage_id', 'team_id');
    }

    public function scopeForTeam(Builder $query, mixed $teamId): Builder
    {
        if (blank($teamId)) {
            return $query;
        }

        return $query->where(fn (Builder $query): Builder => $query
            ->whereDoesntHave('teams')
            ->orWhereHas('teams', fn (Builder $query): Builder => $query->whereKey($teamId)));
    }

    public static function hiddenColumnsForTeam(mixed $teamId): array
    {
        if (blank($teamId)) {
            return [];
        }

        return static::query()
            ->whereNotIn('id', static::query()->forTeam($teamId)->select('id'))
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->all();
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'stage_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $stage): void {
            $stage->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): StageFactory
    {
        return StageFactory::new();
    }
}
