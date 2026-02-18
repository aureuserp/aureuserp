<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class FiscalPositionAccount extends Model
{
    use HasFactory;

    protected $table = 'accounts_fiscal_position_accounts';

    protected $fillable = [
        'fiscal_position_id',
        'company_id',
        'account_source_id',
        'account_destination_id',
        'creator_id',
    ];

    public function fiscalPosition()
    {
        return $this->belongsTo(FiscalPosition::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function accountSource()
    {
        return $this->belongsTo(Account::class, 'account_source_id');
    }

    public function accountDestination()
    {
        return $this->belongsTo(Account::class, 'account_destination_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fiscalPositionAccount) {
            $fiscalPositionAccount->creator_id ??= Auth::id();
        });
    }
}
