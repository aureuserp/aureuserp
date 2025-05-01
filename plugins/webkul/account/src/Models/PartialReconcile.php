<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Support\Models\Currency;

class PartialReconcile extends Model
{
    use HasFactory;

    protected $table = 'accounts_partial_reconciles';

    protected $fillable = [
        'debit_move_id',
        'credit_move_id',
        'full_reconcile_id',
        'exchange_move_id',
        'debit_currency_id',
        'credit_currency_id',
        'company_id',
        'created_by',
        'max_date',
        'amount',
        'debit_amount_currency',
        'credit_amount_currency',
    ];

    public function debitMove()
    {
        return $this->belongsTo(Move::class, 'debit_move_id');
    }

    public function creditMove()
    {
        return $this->belongsTo(Move::class, 'credit_move_id');
    }

    public function fullReconcile()
    {
        return $this->belongsTo(FullReconcile::class, 'full_reconcile_id');
    }

    public function exchangeMove()
    {
        return $this->belongsTo(Move::class, 'exchange_move_id');
    }

    public function debitCurrency()
    {
        return $this->belongsTo(Currency::class, 'debit_currency_id');
    }
}
