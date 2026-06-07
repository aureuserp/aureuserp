<?php

namespace Webkul\Field\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Field extends Model implements Sortable
{
    use SoftDeletes;
    use SortableTrait;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'custom_fields';

    /**
     * Table name.
     *
     * @var string
     */
    protected $casts = [
        'is_multiselect'    => 'boolean',
        'options'           => 'array',
        'form_settings'     => 'array',
        'table_settings'    => 'array',
        'infolist_settings' => 'array',
    ];

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'input_type',
        'is_multiselect',
        'datalist',
        'options',
        'form_settings',
        'use_in_table',
        'table_settings',
        'infolist_settings',
        'sort',
        'customizable_type',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];
}
