<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Tables\OpportunitiesTable;
use Webkul\Crm\Filament\Concerns\ShowsSimilarLeadsBoard;
use Webkul\Crm\Filament\Support\SimilarRecordsPage;

class SimilarOpportunities extends SimilarRecordsPage
{
    use ShowsSimilarLeadsBoard;

    protected static string $resource = OpportunityResource::class;

    protected function getTableViewsListPage(): ListRecords
    {
        return new ListOpportunities;
    }

    protected function configureSimilarTable(Table $table): Table
    {
        return OpportunitiesTable::configure($table);
    }
}
