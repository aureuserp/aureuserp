<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\PointOfSale\Database\Factories\PrinterFactory;
use Webkul\PointOfSale\Enums\PrinterType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Printer extends Model implements Sortable
{
    use BelongsToCompany, HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'pos_printers';

    protected $fillable = [
        'name',
        'printer_type',
        'proxy_ip',
        'sort',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'printer_type' => PrinterType::class,
    ];

    protected $attributes = [
        'printer_type' => 'iot',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'pos_printer_categories', 'printer_id', 'category_id');
    }

    public function configs(): BelongsToMany
    {
        return $this->belongsToMany(Config::class, 'pos_config_printers', 'printer_id', 'config_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Printer $printer) {
            $printer->computeCreatorId();
        });
    }

    protected static function newFactory(): PrinterFactory
    {
        return PrinterFactory::new();
    }
}
