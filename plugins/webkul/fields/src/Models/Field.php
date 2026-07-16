<?php

namespace Webkul\Field\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Field extends Model implements Sortable
{
    use SoftDeletes, SortableTrait;

    /**
     * Resolved custom fields, keyed by customizable model class.
     *
     * Models load their custom fields on every hydration, so without this the
     * listing of N records would issue N queries against this table.
     *
     * @var array<class-string, Collection>
     */
    protected static array $customizableCache = [];

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

    /**
     * Flush the resolved custom fields whenever the underlying rows change, so
     * long-lived processes (queue workers, Octane) don't serve a stale schema.
     */
    protected static function booted(): void
    {
        $flush = fn () => static::flushCustomizableCache();

        static::saved($flush);
        static::deleted($flush);
        static::restored($flush);
    }

    /**
     * Get the custom fields applying to the given model, resolving them once
     * per class for the lifetime of the process.
     */
    public static function forCustomizable(string $model): Collection
    {
        return static::$customizableCache[$model] ??= static::query()
            ->whereIn('customizable_type', static::customizableTypes($model))
            ->get(['code', 'type', 'is_multiselect']);
    }

    /**
     * Forget the resolved custom fields for one model, or for all of them.
     */
    public static function flushCustomizableCache(?string $model = null): void
    {
        if ($model === null) {
            static::$customizableCache = [];

            return;
        }

        unset(static::$customizableCache[$model]);
    }

    /**
     * Resolve a model and its parent classes as customizable types.
     */
    public static function customizableTypes(string $model): array
    {
        $types = [$model];

        foreach (class_parents($model) as $parent) {
            if ($parent === Model::class) {
                break;
            }

            $types[] = $parent;
        }

        return $types;
    }
}
