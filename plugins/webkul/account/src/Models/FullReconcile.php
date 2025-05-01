<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;

class FullReconcile extends Model
{
    use HasFactory;

    protected $table = 'accounts_full_reconciles';

    protected $fillable = [
        'exchange_move_id',
        'created_id',
    ];

    public function exchangeMove()
    {
        return $this->belongsTo(Move::class, 'exchange_move_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_id');
    }
}
