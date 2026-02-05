<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Employee\Database\Factories\DepartureReasonFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;

class DepartureReason extends Model implements Sortable
{
    use HasCustomFields, HasFactory, SortableTrait;

    protected $table = 'employees_departure_reasons';

    protected $fillable = [
        'sort',
        'reason_code',
        'creator_id',
        'name',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($calendar) {
            $calendar->creator_id = filament()->auth()->id();

            $calendar->reason_code = crc32($calendar->name) % 100000;

        });
    }

    /**
     * Get the factory instance for the model.
     */
    protected static function newFactory(): DepartureReasonFactory
    {
        return DepartureReasonFactory::new();
    }
}
