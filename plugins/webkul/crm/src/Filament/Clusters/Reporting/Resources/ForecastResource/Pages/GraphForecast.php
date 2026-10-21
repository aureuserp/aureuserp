<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages;

use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Support\Reports\Pages\ReportGraph;

class GraphForecast extends ReportGraph
{
    protected static string $resource = ForecastResource::class;
}
