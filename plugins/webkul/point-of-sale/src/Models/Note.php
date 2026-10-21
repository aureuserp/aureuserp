<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\PointOfSale\Database\Factories\NoteFactory;
use Webkul\Security\Models\User;

class Note extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'pos_notes';

    protected $fillable = [
        'name',
        'color',
        'sort',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

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

        static::creating(function (Note $note) {
            $note->computeCreatorId();
        });
    }

    protected static function newFactory(): NoteFactory
    {
        return NoteFactory::new();
    }
}
