<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Models\Journal;
use Webkul\Sale\Enums\OrderDisplayType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class OrderTemplate extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'sales_order_templates';

    protected $fillable = [
        'sort',
        'company_id',
        'number_of_days',
        'creator_id',
        'name',
        'note',
        'journal_id',
        'is_active',
        'require_signature',
        'require_payment',
        'prepayment_percentage',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function products()
    {
        return $this
            ->hasMany(OrderTemplateProduct::class, 'order_template_id')
            ->whereNull('display_type');
    }

    public function sections()
    {
        return $this
            ->hasMany(OrderTemplateProduct::class, 'order_template_id')
            ->where('display_type', OrderDisplayType::SECTION->value);
    }

    public function notes()
    {
        return $this
            ->hasMany(OrderTemplateProduct::class, 'order_template_id')
            ->where('display_type', OrderDisplayType::NOTE->value);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderTemplate) {
            $orderTemplate->creator_id ??= Auth::id();
        });
    }
}
