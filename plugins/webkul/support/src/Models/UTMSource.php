<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\UTMSourceFactory;

class UTMSource extends Model
{
    use HasFactory;

    protected $table = 'utm_sources';

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
        return UTMSourceFactory::new();
    }
}
