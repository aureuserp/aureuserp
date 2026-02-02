<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\UTMMediumFactory;

class UTMMedium extends Model
{
    use HasFactory;

    protected $table = 'utm_mediums';

    protected $fillable = [
        'name',
        'creator_id'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory()
    {
        return UTMMediumFactory::new();
    }
}
