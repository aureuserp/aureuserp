<?php

namespace Webkul\Support;

use Filament\Facades\Filament;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\GlobalSearch\Providers\DefaultGlobalSearchProvider;
use Illuminate\Support\Str;

class GlobalSearchProvider extends DefaultGlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        $builder = GlobalSearchResults::make();

        $resources = Filament::getResources();

        usort(
            $resources,
            fn (string $a, string $b): int => ($a::getGlobalSearchSort() ?? 0) <=> ($b::getGlobalSearchSort() ?? 0),
        );

        foreach ($resources as $resource) {
            if (! $resource::canGloballySearch()) {
                continue;
            }

            $resourceResults = $resource::getGlobalSearchResults($query);

            if (! $resourceResults->count()) {
                continue;
            }

            $pluginKey = Str::of($resource)
                ->after('\\')
                ->before('\\')
                ->toString();

            $builder->category($pluginKey.'→'.$resource::getPluralModelLabel(), $resourceResults);
        }

        return $builder;
    }
}
