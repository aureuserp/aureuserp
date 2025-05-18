<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class FiscalPositionTax extends Model
{
    use HasFactory;

    protected $table = 'accounts_fiscal_position_taxes';

    protected $fillable = [
        'fiscal_position_id',
        'company_id',
        'tax_source_id',
        'tax_destination_id',
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

    public function taxSource()
    {
        return $this->belongsTo(Tax::class, 'tax_source_id');
    }

    public function taxDestination()
    {
        return $this->belongsTo(Tax::class, 'tax_destination_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
