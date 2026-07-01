<?php

namespace Webkul\Security\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Security\Models\User;
use Webkul\Security\Support\OwnerSource;

class OwnershipScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $user = auth()->user();

        if (! $user) {
            return;
        }

        if (Gate::allows('bypass_ownership_scope')) {
            return;
        }

        $userIds = bouncer()->getAuthorizedUserIds();

        if ($userIds === null) {
            return;
        }

        if (empty($userIds)) {
            return;
        }

        $sources = method_exists($model, 'ownershipSources')
            ? $model->ownershipSources()
            : [];

        if (empty($sources)) {
            return;
        }

        $builder->where(function (Builder $query) use ($sources, $userIds, $model) {
            foreach ($sources as $source) {
                $this->applySource($query, $source, $userIds, $model);
            }
        });
    }

    protected function applySource(Builder $query, OwnerSource $source, array $userIds, Model $model): void
    {
        match ($source->kind) {
            OwnerSource::KIND_COLUMN    => $this->applyColumn($query, $source, $userIds, $model),
            OwnerSource::KIND_RELATION  => $this->applyRelation($query, $source, $userIds),
            OwnerSource::KIND_PIVOT     => $this->applyPivot($query, $source, $userIds, $model),
            OwnerSource::KIND_FOLLOWERS => $this->applyFollowers($query, $userIds),
            default                     => null,
        };
    }

    protected function applyColumn(Builder $query, OwnerSource $source, array $userIds, Model $model): void
    {
        $column = $source->params['name'];

        if (! $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column)) {
            return;
        }

        $query->orWhereIn($model->getTable().'.'.$column, $userIds);
    }

    protected function applyRelation(Builder $query, OwnerSource $source, array $userIds): void
    {
        $query->orWhereHas($source->params['name'], function (Builder $relation) use ($source, $userIds) {
            $relation->whereIn($source->params['key'], $userIds);
        });
    }

    protected function applyPivot(Builder $query, OwnerSource $source, array $userIds, Model $model): void
    {
        $table = $source->params['table'];
        $foreignKey = $source->params['foreignKey'];
        $relatedKey = $source->params['relatedKey'];

        $query->orWhereExists(function ($exists) use ($table, $foreignKey, $relatedKey, $userIds, $model) {
            $exists->select(DB::raw(1))
                ->from($table)
                ->whereColumn($model->getTable().'.id', $table.'.'.$foreignKey)
                ->whereIn($table.'.'.$relatedKey, $userIds);
        });
    }

    protected function applyFollowers(Builder $query, array $userIds): void
    {
        $partnerIds = User::whereIn('id', $userIds)->pluck('partner_id')->filter()->all();

        if (empty($partnerIds)) {
            return;
        }

        $query->orWhereHas('followers', function (Builder $followers) use ($partnerIds) {
            $followers->whereIn('partner_id', $partnerIds);
        });
    }
}
