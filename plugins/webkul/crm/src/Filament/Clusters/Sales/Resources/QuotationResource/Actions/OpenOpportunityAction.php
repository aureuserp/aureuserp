<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions;

use Filament\Actions\Action;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Sale\Models\Order;

class OpenOpportunityAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'openOpportunity';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/opportunity.actions.open-opportunity.label'))
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->color('gray')
            ->visible(fn (Order $record): bool => $record->opportunity && ! $record->opportunity->trashed() && OpportunityResource::canView($record->opportunity))
            ->url(fn (Order $record): string => OpportunityResource::getUrl('view', ['record' => $record->opportunity_id]));
    }
}
