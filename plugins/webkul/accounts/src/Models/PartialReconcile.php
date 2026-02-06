<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
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
        return $this->belongsTo(MoveLine::class, 'debit_move_id');
    }

    public function creditMove()
    {
        return $this->belongsTo(MoveLine::class, 'credit_move_id');
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($partialReconcile) {
            $partialReconcile->computeCreatedBy();
        });

        static::saving(function ($partialReconcile) {
            $partialReconcile->computeDebitCurrencyId();

            $partialReconcile->computeCreditCurrencyId();

            $partialReconcile->computeMaxDate();
        });
    }

    public function computeCreatedBy()
    {
        $this->created_by = Auth::user()->id ?? null;
    }

    public function computeDebitCurrencyId()
    {
        $this->debit_currency_id = $this->debitMove->currency_id;
    }

    public function computeCreditCurrencyId()
    {
        $this->credit_currency_id = $this->creditMove->currency_id;
    }

    public function computeMaxDate()
    {
        $debitDate = $this->debitMove->move->date;

        $creditDate = $this->creditMove->move->date;

        $this->max_date = ($debitDate > $creditDate) ? $debitDate : $creditDate;
    }
}
