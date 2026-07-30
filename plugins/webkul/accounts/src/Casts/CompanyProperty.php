<?php

namespace Webkul\Account\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use WeakMap;

class CompanyProperty implements CastsAttributes
{
    public const RELATION = 'companyProperties';

    protected static ?WeakMap $pending = null;

    public function __construct(
        protected string $propertyModel,
        protected string $foreignKey,
    ) {}

    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return static::valueFor($model, $this->propertyModel, $this->foreignKey, $key);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        $pending = static::pendingFor($model);

        $pending[$key] = $value;

        static::map()[$model] = $pending;

        return [];
    }

    public static function valueFor(
        Model $model,
        string $propertyModel,
        string $foreignKey,
        string $field,
        ?int $companyId = null,
    ): mixed {
        $pending = static::pendingFor($model);

        if (array_key_exists($field, $pending)) {
            return $pending[$field];
        }

        $properties = static::propertiesFor($model, $propertyModel, $foreignKey, $companyId);

        return $properties
            ? $properties->{$field}
            : ($model->getAttributes()[$field] ?? null);
    }

    public static function propertiesFor(
        Model $model,
        string $propertyModel,
        string $foreignKey,
        ?int $companyId = null,
    ): ?Model {
        $companyId = static::companyIdFor($model, $companyId);

        if (! $companyId || ! $model->exists) {
            return null;
        }

        if (! $model->relationLoaded(static::RELATION)) {
            $model->setRelation(
                static::RELATION,
                $propertyModel::query()->where($foreignKey, $model->getKey())->get(),
            );
        }

        return $model->getRelation(static::RELATION)->firstWhere('company_id', $companyId);
    }

    public static function flush(Model $model, string $propertyModel, string $foreignKey): void
    {
        $pending = static::pendingFor($model);

        if (! $pending) {
            return;
        }

        static::forget($model);

        $companyId = static::companyIdFor($model);

        if (! $companyId) {
            static::storeOnRecord($model, $pending);

            return;
        }

        $keys = [
            $foreignKey  => $model->getKey(),
            'company_id' => $companyId,
        ];

        $exists = $propertyModel::query()->where($keys)->exists();

        if (! $exists && ! array_filter($pending, fn ($value) => filled($value))) {
            return;
        }

        $propertyModel::updateOrCreate($keys, $pending);

        $model->unsetRelation(static::RELATION);
    }

    public static function companyIdFor(Model $model, ?int $companyId = null): ?int
    {
        $companyId = $companyId
            ?: ($model->getAttributes()['company_id'] ?? null)
            ?: current_company_id();

        return $companyId ? (int) $companyId : null;
    }

    protected static function storeOnRecord(Model $model, array $pending): void
    {
        $model->newQueryWithoutScopes()->whereKey($model->getKey())->update($pending);

        $model->setRawAttributes(array_merge($model->getAttributes(), $pending), true);
    }

    protected static function pendingFor(Model $model): array
    {
        return isset(static::map()[$model]) ? static::map()[$model] : [];
    }

    protected static function forget(Model $model): void
    {
        if (isset(static::map()[$model])) {
            unset(static::map()[$model]);
        }
    }

    protected static function map(): WeakMap
    {
        return static::$pending ??= new WeakMap;
    }
}
