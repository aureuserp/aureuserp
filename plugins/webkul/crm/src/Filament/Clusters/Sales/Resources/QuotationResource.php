<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\CreateQuotation;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\EditQuotation;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\GraphQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\KanbanQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ManageDeliveries;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ManageInvoices;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ViewQuotation;
use Webkul\Crm\Filament\Concerns\HasCRMAuthorization;
use Webkul\Crm\Policies\QuotationPolicy;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource as BaseQuotationResource;
use Webkul\Sale\Models\Order;

class QuotationResource extends BaseQuotationResource
{
    use HasCRMAuthorization;

    protected static ?string $slug = 'quotations';

    protected static ?string $cluster = Sales::class;

    protected static ?int $navigationSort = 3;

    protected static bool $isGloballySearchable = false;

    public static function getCRMPolicy(): string
    {
        return QuotationPolicy::class;
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/quotation.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = $page->generateNavigationItems([
            ViewQuotation::class,
            EditQuotation::class,
            ManageInvoices::class,
            ManageDeliveries::class,
        ]);

        $record = method_exists($page, 'getRecord') ? $page->getRecord() : null;
        $opportunity = $record instanceof Order ? $record->opportunity : null;

        if ($opportunity
            && ! $opportunity->trashed()
            && OpportunityResource::canView($opportunity)) {
            $items[] = NavigationItem::make('opportunity')
                ->label(__('crm::filament/clusters/sales/resources/quotation.tabs.opportunity'))
                ->icon('heroicon-o-funnel')
                ->url(OpportunityResource::getUrl('view', ['record' => $opportunity]));
        }

        return $items;
    }

    public static function getViewTabs(string $current): array
    {
        $tab = fn (string $page, string $label, string $icon): NavigationItem => NavigationItem::make($page)
            ->label($label)
            ->icon($icon)
            ->url(static::getUrl($page))
            ->isActiveWhen(fn (): bool => $page === $current);

        return [
            $tab('index', __('crm::filament/clusters/sales/resources/quotation.views.list'), 'heroicon-o-list-bullet'),
            $tab('kanban', __('crm::filament/clusters/sales/resources/quotation.views.kanban'), 'heroicon-o-view-columns'),
            $tab('graph', __('crm::filament/clusters/sales/resources/quotation.views.graph'), 'heroicon-o-chart-bar'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListQuotations::route('/'),
            'kanban'     => KanbanQuotations::route('/kanban'),
            'graph'      => GraphQuotations::route('/graph'),
            'create'     => CreateQuotation::route('/create'),
            'view'       => ViewQuotation::route('/{record}'),
            'edit'       => EditQuotation::route('/{record}/edit'),
            'invoices'   => ManageInvoices::route('/{record}/invoices'),
            'operations' => ManageDeliveries::route('/{record}/deliveries'),
        ];
    }
}
