<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Filament\Resources\Pages\Page;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Concerns\ShowsSimilarLeadsBoard;

class SimilarLeads extends Page
{
    use ShowsSimilarLeadsBoard;

    protected static string $resource = LeadResource::class;
}
