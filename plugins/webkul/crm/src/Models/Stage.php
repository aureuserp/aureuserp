<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        'team_ids',
    ];

    protected ?array $pendingTeamIds = null;

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

    public function getTeamIdsAttribute(): array
    {
        if ($this->pendingTeamIds !== null) {
            return $this->pendingTeamIds;
        }

        return $this->exists ? $this->teams()->pluck('sales_teams.id')->map(fn ($id): int => (int) $id)->all() : [];
    }

    public function setTeamIdsAttribute(mixed $value): void
    {
        $this->pendingTeamIds = array_values(array_unique(array_map('intval', array_filter((array) $value, fn (mixed $id): bool => filled($id)))));
    }

    public function attributesToArray(): array
    {
        return [...parent::attributesToArray(), 'team_ids' => $this->team_ids];
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

        static::saved(function (self $stage): void {
            if ($stage->pendingTeamIds === null) {
                return;
            }

            $visibleTeamIds = Team::query()->pluck('id')->map(fn ($id): int => (int) $id)->all();

            $hiddenTeamIds = DB::table('crm_stage_team_rel')
                ->where('stage_id', $stage->getKey())
                ->whereNotIn('team_id', $visibleTeamIds)
                ->pluck('team_id')
                ->all();

            $stage->teams()->sync([...array_intersect($stage->pendingTeamIds, $visibleTeamIds), ...$hiddenTeamIds]);

            $stage->pendingTeamIds = null;
        });
    }

    protected static function newFactory(): StageFactory
    {
        return StageFactory::new();
    }
}
