<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class MoveReversal extends Model
{
    protected $table = 'accounts_accounts_move_reversals';

    protected $fillable = [
        'company_id',
        'creator_id',
        'reason',
        'date',
    ];

    public function newMoves()
    {
        return $this->belongsToMany(Move::class, 'accounts_accounts_move_reversal_new_move', 'reversal_id', 'new_move_id');
    }

    public function moves()
    {
        return $this->belongsToMany(Move::class, 'accounts_accounts_move_reversal_move', 'reversal_id', 'move_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
