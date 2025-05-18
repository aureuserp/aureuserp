<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;

class AccountAccountTag extends Model
{
    protected $table = 'accounts_account_account_tags';

    protected $fillable = [
        'account_id',
        'account_tag_id',
    ];

    public $timestamps = false;

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function accountTag()
    {
        return $this->belongsTo(Tag::class);
    }
}
