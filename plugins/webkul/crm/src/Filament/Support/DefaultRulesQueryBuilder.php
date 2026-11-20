<?php

namespace Webkul\Crm\Filament\Support;

use Filament\QueryBuilder\Forms\Components\RuleBuilder;
use Filament\Tables\Filters\QueryBuilder;

class DefaultRulesQueryBuilder extends QueryBuilder
{
    public function getSchemaComponents(): array
    {
        $rules = $this->getDefaultState();

        return array_map(
            fn (mixed $component): mixed => $component instanceof RuleBuilder && is_array($rules) ? $component->default($rules) : $component,
            parent::getSchemaComponents(),
        );
    }
}
