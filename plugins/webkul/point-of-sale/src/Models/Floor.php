<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\PointOfSale\Database\Factories\FloorFactory;
use Webkul\Security\Models\User;

class Floor extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'pos_floors';

    protected $fillable = [
        'name',
        'sort',
        'background_color',
        'background_image',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class, 'floor_id');
    }

    public function configs(): BelongsToMany
    {
        return $this->belongsToMany(Config::class, 'pos_config_floors', 'floor_id', 'config_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Floor $floor) {
            $floor->computeCreatorId();
        });

        static::forceDeleted(function (Floor $floor) {
            if ($floor->background_image) {
                Storage::disk('public')->delete($floor->background_image);
            }
        });
    }

    protected static function newFactory(): FloorFactory
    {
        return FloorFactory::new();
    }
}
