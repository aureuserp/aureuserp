<?php

namespace Webkul\Field;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\Field\Models\Field;

class FieldsColumnManager
{
    /**
     * Create a new column for the field
     */
    public static function createColumn(Field $field): void
    {
        $table = static::getTableName($field);

        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($field) {
            if (Schema::hasColumn($table->getTable(), $field->code)) {
                return;
            }

            static::addColumn($table, $field);
        });
    }

    /**
     * Update an existing column
     */
    public static function updateColumn(Field $field): void
    {
        $table = static::getTableName($field);

        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($field) {
            if (! Schema::hasColumn($table->getTable(), $field->code)) {
                static::createColumn($field);

                return;
            }
        });
    }

    /**
     * Delete a column
     */
    public static function deleteColumn(Field $field): void
    {
        $table = static::getTableName($field);

        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($field) {
            if (! Schema::hasColumn($table->getTable(), $field->code)) {
                return;
            }

            $table->dropColumn($field->code);
        });
    }

    /**
     * Add column to table based on field type
     */
    protected static function addColumn(Blueprint $table, Field $field): void
    {
        $typeMethod = static::getColumnType($field);

        // Create the column
        $column = $table->$typeMethod($field->code);

        // Apply common column attributes
        $column->nullable();  // All custom fields are nullable by default

        // Apply specific validations if configured
        if ($field->validations) {
            foreach ($field->validations as $validation) {
                static::applyValidationToColumn($column, $validation);
            }
        }
    }

    /**
     * Determine the appropriate column type for text fields
     */
    protected static function getColumnType(Field $field): string
    {
        return match ($field->type) {
            'text'                           => static::getTextColumnType($field),
            'textarea', 'editor', 'markdown' => 'text',
            'radio'                          => 'string',
            'select'                         => $field->is_multiselect ? 'json' : 'string',
            'checkbox', 'toggle'             => 'boolean',
            'checkbox_list'                  => 'json',
            'datetime'                       => 'datetime',
            'color'                          => 'string',
            default                          => 'string'
        };
    }

    /**
     * Determine the appropriate column type for text fields
     */
    protected static function getTextColumnType(Field $field): string
    {
        return match ($field->input_type) {
            'integer' => 'integer',
            'numeric' => 'decimal',
            default   => 'string'
        };
    }

    /**
     * Apply validation rules to the column
     */
    protected static function applyValidationToColumn($column, array $validation): void
    {
        $rule = $validation['validation'];

        $value = $validation['value'] ?? null;

        switch ($rule) {
            // Add more validation-to-column mappings as needed
        }
    }

    /**
     * Get the table name for the customizable model
     */
    protected static function getTableName(Field $field): string
    {
        $model = app($field->customizable_type);

        return $model->getTable();
    }
}
