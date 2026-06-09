<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'subject',
        'content',
        'description',
        'is_active',
        'sender_name',

    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];
}
