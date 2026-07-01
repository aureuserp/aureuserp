<?php

namespace Webkul\Analytic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Record extends Model
{
    use BelongsToCompany;

    protected $table = 'analytic_records';

    protected $fillable = [
        'type',
        'name',
        'date',
        'amount',
        'unit_amount',
        'partner_id',
        'company_id',
        'user_id',
        'creator_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($record) {
            $record->creator_id ??= Auth::id();
        });
    }
}
