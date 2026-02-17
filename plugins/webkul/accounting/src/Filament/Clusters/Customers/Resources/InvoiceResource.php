<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources;

use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Webkul\Account\Filament\Resources\InvoiceResource as BaseInvoiceResource;
use Webkul\Accounting\Filament\Clusters\Customers;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ManagePayments;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ViewInvoice;
use Webkul\Accounting\Livewire\InvoiceSummary;
use Webkul\Accounting\Models\Invoice;
use Webkul\Security\Traits\HasResourcePermissionQuery;
use Webkul\Support\Filament\Forms\Components\Repeater;

class InvoiceResource extends BaseInvoiceResource
{
    use HasResourcePermissionQuery;

    protected static ?string $model = Invoice::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = true;

    protected static ?string $cluster = Customers::class;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/invoice.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/invoice.navigation.title');
    }

    public static function getSummaryComponent()
    {
        return InvoiceSummary::class;
    }

    public static function getProductRepeater(): Repeater
    {
        return parent::getProductRepeater()
            ->extraItemActions([
                Action::make('openProduct')
                    ->tooltip('Open product')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (array $arguments, Get $get): ?string => ProductResource::getUrl('edit', [
                        'record' => $get("products.{$arguments['item']}.product_id"),
                    ]))
                    ->openUrlInNewTab()
                    ->visible(fn (array $arguments, Get $get): bool => filled($get("products.{$arguments['item']}.product_id"))),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewInvoice::class,
            EditInvoice::class,
            ManagePayments::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListInvoices::route('/'),
            'create'   => CreateInvoice::route('/create'),
            'view'     => ViewInvoice::route('/{record}'),
            'edit'     => EditInvoice::route('/{record}/edit'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
