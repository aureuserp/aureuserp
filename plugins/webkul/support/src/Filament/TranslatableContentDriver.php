<?php

namespace Webkul\Support\Filament;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\SpatieTranslatable\SpatieTranslatableContentDriver;

class TranslatableContentDriver extends SpatieTranslatableContentDriver
{
    public function applySearchConstraintToQuery(Builder $query, string $column, string $search, string $whereClause, ?bool $isCaseInsensitivityForced = null): Builder
    {
        return parent::applySearchConstraintToQuery($query, $column, $search, $whereClause, $isCaseInsensitivityForced ?? true);
    }

    public function getRecordAttributesToArray(Model $record): array
    {
        $attributes = $record->attributesToArray();

        if (! method_exists($record, 'getTranslatableAttributes')) {
            return $attributes;
        }

        if (! method_exists($record, 'getTranslation')) {
            return $attributes;
        }

        foreach ($record->getTranslatableAttributes() as $attribute) {
            $attributes[$attribute] = $record->getTranslation($attribute, $this->activeLocale, useFallbackLocale: true);
        }

        return $attributes;
    }
}
