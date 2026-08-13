<?php

namespace Webkul\Support\Filament;

use Illuminate\Database\Eloquent\Builder;
use LaraZeus\SpatieTranslatable\SpatieTranslatableContentDriver;

class TranslatableContentDriver extends SpatieTranslatableContentDriver
{
    public function applySearchConstraintToQuery(Builder $query, string $column, string $search, string $whereClause, ?bool $isCaseInsensitivityForced = null): Builder
    {
        return parent::applySearchConstraintToQuery($query, $column, $search, $whereClause, $isCaseInsensitivityForced ?? true);
    }
}
