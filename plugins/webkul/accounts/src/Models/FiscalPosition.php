<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Database\Factories\FiscalPositionFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;

class FiscalPosition extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'accounts_fiscal_positions';

    protected $fillable = [
        'sort',
        'company_id',
        'country_id',
        'country_group_id',
        'creator_id',
        'zip_from',
        'zip_to',
        'foreign_vat',
        'name',
        'notes',
        'auto_reply',
        'vat_required',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function countryGroup()
    {
        return $this->belongsTo(Country::class, 'country_group_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function taxes()
    {
        return $this->hasMany(FiscalPositionTax::class, 'fiscal_position_id');
    }

    public function accounts()
    {
        return $this->hasMany(FiscalPositionAccount::class, 'fiscal_position_id');
    }

    public function mapAccount($account)
    {
        $mapping = $this->accounts()
            ->where('account_source_id', $account->id)
            ->first();

        return $mapping
            ? Account::find($mapping->account_destination_id)
            : $account;
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fiscalPosition) {
            $fiscalPosition->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory()
    {
        return FiscalPositionFactory::new();
    }
}
