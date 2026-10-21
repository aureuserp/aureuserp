<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\PointOfSale\Database\Factories\BillFactory;
use Webkul\Security\Models\User;

class Bill extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'pos_bills';

    protected $fillable = [
        'name',
        'value',
        'sort',
        'is_for_all_configs',
        'creator_id',
    ];

    protected $casts = [
        'value'              => 'decimal:4',
        'is_for_all_configs' => 'boolean',
    ];

    protected $attributes = [
        'value'              => 0,
        'is_for_all_configs' => true,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function configs(): BelongsToMany
    {
        return $this->belongsToMany(Config::class, 'pos_config_bills', 'bill_id', 'config_id');
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

        static::creating(function (Bill $bill) {
            $bill->computeCreatorId();
        });
    }

    protected static function newFactory(): BillFactory
    {
        return BillFactory::new();
    }
}
