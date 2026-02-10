<?php

namespace Webkul\TableViews\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;

class TableView extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'color',
        'is_public',
        'filters',
        'filterable_type',
        'user_id',
    ];

    protected $casts = [
        'filters' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
