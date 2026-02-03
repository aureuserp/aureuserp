<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Illuminate\Support\Facades\Auth;

class MoveReversal extends Model
{
    protected $table = 'accounts_accounts_move_reversals';

    protected $fillable = [
        'reason',
        'date',
        'journal_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($moveReversal) {
            $moveReversal->creator_id = Auth::id();

            $moveReversal->company_id = Auth::user()->default_company_id;
        });
    }
}
