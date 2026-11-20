<?php

namespace Webkul\Crm\Filament\Concerns;

use BackedEnum;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use ReflectionMethod;
use UnitEnum;

trait HasCRMAuthorization
{
    abstract public static function getCRMPolicy(): string;

    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        if (static::shouldSkipAuthorization()) {
            return Response::allow();
        }

        $user = Filament::auth()->user();

        $ability = match (true) {
            $action instanceof BackedEnum => (string) $action->value,
            $action instanceof UnitEnum   => $action->name,
            default                       => $action,
        };

        $policy = app(static::getCRMPolicy());

        if (! $user
            || ! method_exists($policy, $ability)) {
            return Response::deny();
        }

        $arguments = $record ? [$user, $record] : [$user];

        if ((new ReflectionMethod($policy, $ability))->getNumberOfRequiredParameters() > count($arguments)) {
            return Response::deny();
        }

        return $policy->{$ability}(...$arguments) ? Response::allow() : Response::deny();
    }
}
