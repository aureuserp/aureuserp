<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeadRole extends Model
{
    protected $table = 'crm_lead_roles';

    protected $fillable = [
        'name',
        'sort',
        'creator_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $record): void {
            $record->creator_id ??= Auth::id();
        });
    }
}
