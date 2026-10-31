<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\NewQuotationAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Models\Lead;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Quotation;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageQuotations extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = OpportunityResource::class;

    protected static string $relationship = 'quotations';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;

        return $record instanceof Lead && OpportunityResource::canView($record) && QuotationResource::canViewAny();
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity.quotations.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity.quotations.title');
    }

    public static function navigationItems(Page $page): array
    {
        $record = method_exists($page, 'getRecord') ? $page->getRecord() : null;

        if (! $record instanceof Lead
            || $record->isLead()
            || ! static::canAccess(['record' => $record])) {
            return [];
        }

        $count = $record->quotations()->where('state', '!=', OrderState::CANCEL)->count();

        return [
            NavigationItem::make('quotations')
                ->label(static::getNavigationLabel())
                ->icon('heroicon-o-document-text')
                ->badge($count ? (string) $count : null)
                ->url(OpportunityResource::getUrl('quotations', ['record' => $record]))
                ->isActiveWhen(fn (): bool => $page instanceof static),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            NewQuotationAction::make()->record($this->getRecord()),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['partner', 'user', 'currency']))
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.quotations.columns.number'))
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('date_order')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.quotations.columns.date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.quotations.columns.customer')),
                TextColumn::make('user.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.quotations.columns.salesperson'))
                    ->placeholder('—'),
                TextColumn::make('amount_total')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.quotations.columns.total'))
                    ->money(fn (Quotation $record): ?string => $record->currency?->code)
                    ->sortable(),
                TextColumn::make('state')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.quotations.columns.status'))
                    ->badge(),
            ])
            ->defaultSort('id', 'desc')
            ->recordUrl(fn (Model $record): ?string => QuotationResource::canView($record) ? QuotationResource::getUrl('view', ['record' => $record]) : null)
            ->emptyStateHeading(__('crm::filament/clusters/sales/resources/opportunity.quotations.empty.heading'))
            ->emptyStateDescription(__('crm::filament/clusters/sales/resources/opportunity.quotations.empty.description'));
    }
}
