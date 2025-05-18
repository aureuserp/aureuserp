<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;

class CashRounding extends Model
{
    use HasFactory;

    protected $table = 'accounts_cash_roundings';

    protected $fillable = [
        'creator_id',
        'strategy',
        'rounding_method',
        'name',
        'rounding',
        'profit_account_id',
        'loss_account_id',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function profitAccount()
    {
        return $this->belongsTo(Account::class, 'profit_account_id');
    }

    public function lossAccount()
    {
        return $this->belongsTo(Account::class, 'loss_account_id');
    }
}
