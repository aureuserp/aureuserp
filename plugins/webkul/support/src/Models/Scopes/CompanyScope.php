<?php

namespace Webkul\Support\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Webkul\Support\Services\CompanyContext;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        if (! auth()->check()) {
            return;
        }

        $context = app(CompanyContext::class);

        if ($context->bypassed()) {
            return;
        }

        $ids = $context->activeIds();

        $table = $model->getTable();

        $builder->where(function (Builder $query) use ($ids, $table) {
            $query->whereIn($table.'.company_id', $ids)
                ->orWhereNull($table.'.company_id');
        });
    }
}
