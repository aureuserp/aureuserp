<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Resources\Pages\Page;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Concerns\ShowsSimilarLeadsBoard;

class SimilarOpportunities extends Page
{
    use ShowsSimilarLeadsBoard;

    protected static string $resource = OpportunityResource::class;
}
