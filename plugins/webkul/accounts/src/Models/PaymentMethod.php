<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'accounts_payment_methods';

    protected $fillable = ['code', 'payment_type', 'name', 'creator_id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function accountMovePayment()
    {
        return $this->hasMany(Move::class, 'payment_id');
    }
}
