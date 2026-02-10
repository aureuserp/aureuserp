<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Employee\Database\Factories\EmploymentTypeFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;

class EmploymentType extends Model implements Sortable
{
    use HasCustomFields, HasFactory, SortableTrait;

    protected $table = 'employees_employment_types';

    protected $fillable = [
        'name',
        'country_id',
        'creator_id',
        'code',
        'sort',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employmentType) {
            $employmentType->code ??= $employmentType->name;

            $employmentType->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): EmploymentTypeFactory
    {
        return EmploymentTypeFactory::new();
    }
}
