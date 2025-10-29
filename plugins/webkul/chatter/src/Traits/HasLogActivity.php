<?php

namespace Webkul\Chatter\Traits;

use BackedEnum;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait HasLogActivity
{
    /**
     * Boot the trait
     */
    public static function bootHasLogActivity()
    {
        static::created(fn (Model $model) => $model->logModelActivity('created'));
        static::updated(fn (Model $model) => $model->logModelActivity('updated'));

        if (method_exists(static::class, 'bootSoftDeletes')) {
            static::deleted(function (Model $model) {
                if (method_exists($model, 'trashed') && $model->trashed()) {
                    $model->logModelActivity('soft_deleted');
                } else {
                    $model->logModelActivity('hard_deleted');
                }
            });
            static::restored(fn (Model $model) => $model->logModelActivity('restored'));
        } else {
            static::deleting(fn (Model $model) => $model->logModelActivity('deleted'));
        }
    }

    /**
     * Log model activity
     */
    public function logModelActivity(string $event): ?Model
    {
        $user = filament()->auth()->user();

        try {
            $changes = $this->determineChanges($event);

            if (collect($changes)->isEmpty()) {
                return null;
            }

            return $this->addMessage([
                'type'         => 'notification',
                'log_name'     => 'default',
                'body'         => $this->generateActivityDescription($event),
                'subject_type' => $this->getMorphClass(),
                'subject_id'   => $this->getKey(),
                'causer_type'  => $user->getMorphClass(),
                'causer_id'    => $user->id,
                'event'        => $event,
                'properties'   => $changes,
            ]);
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * Get attributes to be logged.
     * Override this in your model to specify which attributes to log
     */
    protected function getLogAttributes(): array
    {
        $normalized = [];

        foreach (property_exists($this, 'logAttributes') ? $this->logAttributes : [] as $key => $value) {
            if (is_int($key)) {
                $normalized[$value] = $value;
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

    /**
     * Get the relationship and attribute to log from the attribute key
     */
    protected function parseRelationAttribute(string $key): ?array
    {
        if (! str_contains($key, '.')) {
            return null;
        }

        $parts = explode('.', $key);
        $relation = $parts[0];
        $attribute = $parts[1];

        return [$relation, $attribute];
    }

    /**
     * Get related model value
     */
    protected function getRelatedValue($relation, $id, $attribute)
    {
        try {
            if (! method_exists($this, $relation)) {
                return null;
            }

            $relatedModel = $this->$relation()->getRelated();
            $instance = $relatedModel->find($id);

            return $instance ? $instance->$attribute : null;
        } catch (Exception $e) {
            Log::error("Error getting related value for {$relation}.{$attribute}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Get changes for all monitored attributes
     */
    protected function getAllAttributeChanges(): array
    {
        $changes = [];
        $original = $this->getOriginal();
        $current = $this->getDirty();
        $logAttributes = $this->getLogAttributes();

        foreach ($logAttributes as $key => $title) {
            if ($parsed = $this->parseRelationAttribute($key)) {
                [$relation, $attribute] = $parsed;
                $changes[$title] = $this->getRelationshipChanges($relation, $attribute, $original, $current);
            } else {
                $changes[$title] = $this->getDirectAttributeChanges($key, $original, $current);
            }
        }

        return array_filter($changes);
    }

    /**
     * Get changes for relationship attributes
     */
    protected function getRelationshipChanges(string $relation, string $attribute, array $original, array $current): ?array
    {
        try {
            if (! method_exists($this, $relation)) {
                return null;
            }

            $foreignKey = $this->$relation()->getForeignKeyName();

            if (array_key_exists($foreignKey, $current)) {
                $oldValue = $this->getRelatedValue($relation, $original[$foreignKey] ?? null, $attribute);
                $newValue = $this->getRelatedValue($relation, $current[$foreignKey], $attribute);

                if ($oldValue !== $newValue) {
                    return [
                        'type'      => 'modified',
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                        'relation'  => $relation,
                        'attribute' => $attribute,
                    ];
                }
            }
        } catch (Exception $e) {
            Log::error("Error tracking relationship changes for {$relation}.{$attribute}: ".$e->getMessage());
        }

        return null;
    }

    protected function getDirectAttributeChanges(string $key, array $original, array $current): ?array
    {
        if (array_key_exists($key, $current)) {
            $oldValue = $this->formatAttributeValue($key, $original[$key] ?? null);
            $newValue = $this->formatAttributeValue($key, $current[$key]);

            if ($oldValue !== $newValue) {
                return [
                    'type'      => is_null($oldValue) ? 'added' : 'modified',
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ];
            }
        }

        return null;
    }

    /**
     * Determine changes in the model
     */
    protected function determineChanges(string $event): ?array
    {
        return match ($event) {
            'created' => $this->getModelAttributes(),
            'updated' => $this->getAllAttributeChanges(),
            default   => null
        };
    }

    /**
     * Get model attributes
     */
    protected function getModelAttributes(): array
    {
        $logAttributes = $this->getLogAttributes();
        $attributes = [];

        foreach ($logAttributes as $key) {
            if ($parsed = $this->parseRelationAttribute($key)) {
                [$relation, $attribute] = $parsed;
                $foreignKey = $this->$relation()->getForeignKeyName();
                $value = $this->getRelatedValue($relation, $this->$foreignKey, $attribute);
                $attributes[$key] = $value;
            } else {
                $value = $this->getAttribute($key);
                $attributes[$key] = $this->formatAttributeValue($key, $value);
            }
        }

        return $attributes;
    }

    /**
     * Get updated attributes
     */
    protected function getUpdatedAttributes(): array
    {
        $original = $this->getOriginal();
        $current = $this->getDirty();
        $logAttributes = $this->getLogAttributes();
        $changes = [];

        foreach ($logAttributes as $key) {
            if ($parsed = $this->parseRelationAttribute($key)) {
                [$relation, $attribute] = $parsed;
                $foreignKey = $this->$relation()->getForeignKeyName();

                if (array_key_exists($foreignKey, $current)) {
                    $oldValue = $this->getRelatedValue($relation, $original[$foreignKey] ?? null, $attribute);
                    $newValue = $this->getRelatedValue($relation, $current[$foreignKey], $attribute);

                    if ($oldValue !== $newValue) {
                        $changes[$key] = [
                            'type'      => is_null($oldValue) ? 'added' : 'modified',                            'old_value' => $oldValue,
                            'new_value' => $newValue,
                        ];
                    }
                }
            } else {
                if (array_key_exists($key, $current)) {
                    $oldValue = $this->formatAttributeValue($key, $original[$key] ?? null);
                    $newValue = $this->formatAttributeValue($key, $current[$key]);

                    if ($oldValue !== $newValue) {
                        $changes[$key] = [
                            'type'      => array_key_exists($key, $original) ? 'modified' : 'added',
                            'old_value' => $oldValue,
                            'new_value' => $newValue,
                        ];
                    }
                }
            }
        }

        return $changes;
    }

    /**
     * Format attribute value
     */
    protected function formatAttributeValue(string $key, $value): mixed
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (
            $value !== null
            && isset($this->casts[$key])
        ) {
            $castType = $this->casts[$key];

            if (class_exists($castType) && is_subclass_of($castType, BackedEnum::class)) {
                try {
                    if ($value instanceof BackedEnum) {
                        if (method_exists($value, 'getLabel')) {
                            return $value->getLabel();
                        }

                        return $value->value;
                    }

                    $enumInstance = $castType::from($value);

                    if (method_exists($enumInstance, 'getLabel')) {
                        return $enumInstance->getLabel();
                    }

                    return $enumInstance->value;
                } catch (Exception $e) {
                    return $value;
                }
            }
        }

        if (
            ! is_array($value)
            && json_decode($value, true)
        ) {
            $value = json_decode($value, true);
        }

        if (is_array($value)) {
            static::ksortRecursive($value);
        }

        return $value;
    }

    /**
     * Sort array recursively
     */
    protected static function ksortRecursive(&$array)
    {
        if (! is_array($array)) {
            return;
        }

        ksort($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                static::ksortRecursive($value);
            }
        }
    }

    /**
     * Generate activity description
     */
    protected function generateActivityDescription(string $event): string
    {
        $modelName = Str::headline(class_basename(static::class));

        return match ($event) {
            'created'      => __('chatter::traits/has-log-activity.activity-log-failed.events.created', [
                'model' => $modelName,
            ]),
            'updated'      => __('chatter::traits/has-log-activity.activity-log-failed.events.updated', [
                'model' => $modelName,
            ]),
            'deleted'      => __('chatter::traits/has-log-activity.activity-log-failed.events.deleted', [
                'model' => $modelName,
            ]),
            'soft_deleted' => __('chatter::traits/has-log-activity.activity-log-failed.events.soft-deleted', [
                'model' => $modelName,
            ]),
            'hard_deleted' => __('chatter::traits/has-log-activity.activity-log-failed.events.hard-deleted', [
                'model' => $modelName,
            ]),
            'restored'     => __('chatter::traits/has-log-activity.activity-log-failed.events.restored', [
                'model' => $modelName,
            ]),
            default        => $event
        };
    }
}
