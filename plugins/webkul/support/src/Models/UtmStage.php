<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\UtmStageFactory;

class UtmStage extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'utm_stages';

    protected $fillable = [
        'sort',
        'name',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory(): UtmStageFactory
    {
        return UtmStageFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($utmStage) {
            $utmStage->creator_id ??= Auth::id();
        });
    }
}
