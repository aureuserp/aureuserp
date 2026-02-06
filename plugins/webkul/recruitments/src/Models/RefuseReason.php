<?php

namespace Webkul\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;
use Illuminate\Support\Facades\Auth;

class RefuseReason extends Model implements Sortable
{
    use SortableTrait;

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    protected $table = 'recruitments_refuse_reasons';

    protected $fillable = ['creator_id', 'sort', 'name', 'template', 'is_active'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($refuseReason) {
            $refuseReason->creator_id ??= Auth::id();
        });
    }
}
