<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Webkul\Crm\Enums\QuotationBoardGroup;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;

class KanbanQuotations extends ListQuotations
{
    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/sales/resources/quotation.views.kanban');
    }

    public function getRecordNavigationTabs(): array
    {
        return QuotationResource::getViewTabs('kanban');
    }

    public function getBoardGroup(): ?QuotationBoardGroup
    {
        return parent::getBoardGroup() ?? QuotationBoardGroup::STATUS;
    }

    protected function getTableViewsFilterableType(): string
    {
        return ListQuotations::class;
    }
}
