<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;

class AccountTax extends Model
{
    protected $table = 'accounts_account_taxes';

    protected $fillable = [
        'account_id',
        'tax_id',
    ];

    public $timestamps = false;

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
