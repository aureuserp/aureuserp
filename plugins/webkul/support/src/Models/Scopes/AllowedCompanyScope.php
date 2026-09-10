<?php

namespace Webkul\Support\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;
use Webkul\Support\Services\CompanyContext;

class AllowedCompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        $context = app(CompanyContext::class);

        $user = $context->internalUser();

        if (! $user) {
            return;
        }

        if ($context->seesAllCompanies()) {
            return;
        }

        $ids = DB::table('user_allowed_companies')
            ->where('user_id', $user->getKey())
            ->pluck('company_id')
            ->all();

        $builder->whereIn($model->getTable().'.id', $ids);
    }
}
