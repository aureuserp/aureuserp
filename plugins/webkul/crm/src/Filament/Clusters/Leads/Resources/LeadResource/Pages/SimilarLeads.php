<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Tables\LeadsTable;
use Webkul\Crm\Filament\Concerns\ShowsSimilarLeadsBoard;
use Webkul\Crm\Filament\Support\SimilarRecordsPage;

class SimilarLeads extends SimilarRecordsPage
{
    use ShowsSimilarLeadsBoard;

    protected static string $resource = LeadResource::class;

    protected function getTableViewsListPage(): ListRecords
    {
        return new ListLeads;
    }

    protected function configureSimilarTable(Table $table): Table
    {
        return LeadsTable::configure($table);
    }
}
