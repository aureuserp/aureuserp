<?php

namespace Webkul\Partner\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Partner\Database\Factories\BankAccountFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Bank;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'partners_bank_accounts';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'account_number',
        'account_holder_name',
        'is_active',
        'can_send_money',
        'creator_id',
        'partner_id',
        'bank_id',
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected $casts = [
        'is_active'      => 'boolean',
        'can_send_money' => 'boolean',
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Bootstrap any application services.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bankAccount) {
            $bankAccount->account_holder_name = $bankAccount->partner->name;
        });

        static::updating(function ($bankAccount) {
            $bankAccount->account_holder_name = $bankAccount->partner->name;
        });
    }

    protected static function newFactory(): BankAccountFactory
    {
        return BankAccountFactory::new();
    }
}
