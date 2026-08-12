<?php

namespace Webkul\Support\Traits;

use Illuminate\Routing\Router;

trait HasRouterMacros
{
    protected function registerRouterMacros(): void
    {
        Router::macro('softDeletableApiResource', function ($name, $controller, array $options = []) {
            $this->apiResource($name, $controller, $options);

            $segments = explode('.', $name);

            $path = collect($segments)
                ->map(function ($segment, $index) use ($segments) {
                    if ($index === 0) {
                        return $segment;
                    }

                    $parentParam = str_replace('-', '_', str($segments[$index - 1])->singular()->toString()).'_id';

                    return "{{$parentParam}}/{$segment}";
                })
                ->implode('/');

            $this->post("{$path}/{id}/restore", [$controller, 'restore'])
                ->name("{$name}.restore");

            $this->delete("{$path}/{id}/force", [$controller, 'forceDestroy'])
                ->name("{$name}.force-destroy");
        });
    }
}
