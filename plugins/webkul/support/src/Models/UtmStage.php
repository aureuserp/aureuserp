<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;

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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
