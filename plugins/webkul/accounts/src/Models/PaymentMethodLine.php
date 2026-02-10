<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Accounting\Models\Journal;
use Webkul\Security\Models\User;

class PaymentMethodLine extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'accounts_payment_method_lines';

    protected $fillable = [
        'sort',
        'payment_method_id',
        'payment_account_id',
        'journal_id',
        'name',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentAccount()
    {
        return $this->belongsTo(Account::class);
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function defaultAccount()
    {
        return $this->hasOneThrough(
            Account::class,
            Journal::class,
            'id',
            'id',
            'journal_id',
            'default_account_id'
        );
    }

    public function getCodeAttribute()
    {
        return $this->paymentMethod->code;
    }

    public function computeName()
    {
        $this->name = $this->paymentMethod->name;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paymentMethodLine) {
            $paymentMethodLine->creator_id ??= Auth::id();
        });
    }
}
