<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class BillOfMaterialLine extends Model
{
    use HasFactory;

    protected $table = 'manufacturing_bill_of_material_lines';

    protected $fillable = [
        'sort',
        'quantity',
        'is_manual_consumption',
        'bill_of_material_id',
        'product_id',
        'company_id',
        'uom_id',
        'operation_id',
        'creator_id',
    ];

    protected $casts = [
        'quantity'              => 'decimal:4',
        'is_manual_consumption' => 'boolean',
    ];

    public function billOfMaterial(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterial::class, 'bill_of_material_id')->withTrashed();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class)->withTrashed();
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class, 'operation_id')->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttributeValue::class, 'manufacturing_bill_of_material_line_attribute_values', 'bill_of_material_line_id', 'product_attribute_value_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $line): void {
            $authUser = Auth::user();

            $line->creator_id ??= $authUser?->id;
            $line->company_id ??= $line->company_id ?? $authUser?->default_company_id;
        });
    }

    public function skipBomLine($product)
    {
        return false;
    }
}
