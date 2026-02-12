<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Product\Database\Factories\PackagingFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Packaging extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'products_packagings';

    protected $fillable = [
        'name',
        'barcode',
        'qty',
        'sort',
        'product_id',
        'company_id',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($packaging) {
            $packaging->creator_id ??= Auth::id();

            $packaging->company_id ??= Auth::user()?->default_company_id;
        });
    }

    protected static function newFactory(): PackagingFactory
    {
        return PackagingFactory::new();
    }
}
