<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Database\Factories\RequisitionLineFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class RequisitionLine extends Model
{
    use HasFactory;

    protected $table = 'purchases_requisition_lines';

    protected $fillable = [
        'qty',
        'price_unit',
        'requisition_id',
        'product_id',
        'uom_id',
        'company_id',
        'creator_id',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(Requisition::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): RequisitionLineFactory
    {
        return RequisitionLineFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($requisitionLine) {
            $requisitionLine->creator_id ??= Auth::id();
        });
    }
}
