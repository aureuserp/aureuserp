<?php

namespace Webkul\Field\Traits;

use Exception;
use Webkul\Field\Models\Field;

trait HasCustomFields
{
    /**
     * Boot the trait
     */
    protected static function bootHasCustomFields()
    {
        static::retrieved(fn ($model) => $model->loadCustomFields());

        static::creating(fn ($model) => $model->loadCustomFields());

        static::updating(fn ($model) => $model->loadCustomFields());
    }

    /**
     * Fill the model with an array of attributes.
     */
    public function fill(array $attributes): static
    {
        $this->loadCustomFields();

        return parent::fill($attributes);
    }

    /**
     * Load and merge custom fields into the model.
     */
    protected function loadCustomFields()
    {
        try {
            $customFields = $this->getCustomFields();

            $this->mergeFillable($customFields->pluck('code')->toArray());

            $this->mergeCasts($customFields->select('code', 'type', 'is_multiselect')->get());
        } catch (Exception) {
        }
    }

    /**
     * Get all custom field codes for this model
     */
    protected function getCustomFields()
    {
        return Field::whereIn('customizable_type', Field::customizableTypes(static::class));
    }

    /**
     * Add custom fields to fillable
     */
    public function mergeFillable(array $attributes): void
    {
        $this->fillable = array_unique(array_merge($this->fillable, $attributes));
    }

    /**
     * Add custom fields to fillable
     *
     * @param  array  $casts
     * @return $this
     */
    public function mergeCasts($attributes)
    {
        if (is_array($attributes)) {
            parent::mergeCasts($attributes);

            return $attributes;
        }

        foreach ($attributes as $attribute) {
            match ($attribute->type) {
                'select'        => $this->casts[$attribute->code] = $attribute->is_multiselect ? 'array' : 'string',
                'checkbox'      => $this->casts[$attribute->code] = 'boolean',
                'toggle'        => $this->casts[$attribute->code] = 'boolean',
                'checkbox_list' => $this->casts[$attribute->code] = 'array',
                default         => $this->casts[$attribute->code] = 'string',
            };
        }

        return $this;
    }
}
