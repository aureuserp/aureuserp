<?php

namespace Webkul\Security\Filament\Resources\Companies\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Security\Filament\Resources\Companies\CompanyResource;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListCompanies extends ListRecords
{
    use HasTableViews;

    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon('heroicon-o-plus-circle')
                ->label(__('security::filament/resources/company/pages/list-company.header-actions.create.label')),
        ];
    }
}
