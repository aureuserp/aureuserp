<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsWonAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Tables\OpportunitiesTable;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Models\Lead;
use Webkul\Sale\Models\Order;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageOpportunity extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = QuotationResource::class;

    protected static string $relationship = 'opportunity';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    public static function canAccess(array $parameters = []): bool
    {
        $quotation = $parameters['record'] ?? null;
        $opportunity = $quotation instanceof Order ? $quotation->opportunity : null;

        return parent::canAccess($parameters)
            && $opportunity
            && ! $opportunity->trashed()
            && OpportunityResource::canView($opportunity);
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/quotation.tabs.opportunity');
    }

    public function table(Table $table): Table
    {
        return OpportunitiesTable::configure($table)
            ->modelLabel(OpportunityResource::getModelLabel())
            ->pluralModelLabel(OpportunityResource::getPluralModelLabel())
            ->recordUrl(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record]))
            ->groups([])
            ->filters([])
            ->recordActions([
                ActivityTableAction::make()
                    ->tooltip(__('crm::filament/clusters/sales/resources/opportunity.table.actions.schedule-activity.label'))
                    ->visible(fn (Lead $record): bool => OpportunityResource::canEdit($record)),
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record])),
                    EditAction::make()
                        ->url(fn (Lead $record): string => OpportunityResource::getUrl('edit', ['record' => $record])),
                    MarkAsWonAction::make(),
                    MarkAsLostAction::make(),
                ]),
            ])
            ->toolbarActions([])
            ->paginated(false);
    }
}
