<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Security\Models\User;

class Incoterm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounts_incoterms';

    protected $fillable = [
        'code',
        'name',
        'creator_id',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
