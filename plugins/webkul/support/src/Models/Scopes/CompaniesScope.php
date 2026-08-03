<?php

namespace Webkul\Support\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Webkul\Support\Services\CompanyContext;

class CompaniesScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
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

        $relation = method_exists($model, 'companyScopeRelation')
            ? $model->companyScopeRelation()
            : 'companies';

        $builder->where(function (Builder $query) use ($ids, $relation) {
            $query->whereHas($relation, fn (Builder $related) => $related->whereIn('companies.id', $ids))
                ->orWhereDoesntHave($relation);
        });
    }
}
