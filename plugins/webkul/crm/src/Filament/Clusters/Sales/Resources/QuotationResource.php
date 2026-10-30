<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\CreateQuotation;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\EditQuotation;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ManageDeliveries;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ManageInvoices;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ManageOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ViewQuotation;
use Webkul\Crm\Filament\Concerns\HasCRMAuthorization;
use Webkul\Crm\Policies\QuotationPolicy;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource as BaseQuotationResource;

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
        return $page->generateNavigationItems([
            ViewQuotation::class,
            EditQuotation::class,
            ManageInvoices::class,
            ManageDeliveries::class,
            ManageOpportunity::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'       => ListQuotations::route('/'),
            'kanban'      => ListQuotations::route('/kanban'),
            'graph'       => ListQuotations::route('/graph'),
            'create'      => CreateQuotation::route('/create'),
            'view'        => ViewQuotation::route('/{record}'),
            'edit'        => EditQuotation::route('/{record}/edit'),
            'invoices'    => ManageInvoices::route('/{record}/invoices'),
            'operations'  => ManageDeliveries::route('/{record}/deliveries'),
            'opportunity' => ManageOpportunity::route('/{record}/opportunity'),
        ];
    }
}
