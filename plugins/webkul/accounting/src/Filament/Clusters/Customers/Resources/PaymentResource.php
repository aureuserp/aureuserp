<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\PaymentResource as BasePaymentResource;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\ViewJournalEntry;
use Webkul\Accounting\Filament\Clusters\Customers;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages\CreatePayment;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages\EditPayment;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages\ListPayments;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages\ManageInvoices;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages\ViewPayment;
use Webkul\Accounting\Models\Payment;

class PaymentResource extends BasePaymentResource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = true;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Customers::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/payment.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/payment.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $navigationItems = $page->generateNavigationItems([
            ViewPayment::class,
            EditPayment::class,
            ManageInvoices::class,
        ]);

        if (($record = $page->getRecord())?->move_id) {
            $navigationItems[] = NavigationItem::make(__('accounting::filament/clusters/customers/resources/payment.record-sub-navigation.journal-entry'))
                ->icon('heroicon-o-receipt-percent')
                ->url(ViewJournalEntry::getUrl(['record' => $record->move_id]));
        }

        return $navigationItems;
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListPayments::route('/'),
            'create'   => CreatePayment::route('/create'),
            'view'     => ViewPayment::route('/{record}'),
            'edit'     => EditPayment::route('/{record}/edit'),
            'invoices' => ManageInvoices::route('/{record}/invoices'),
        ];
    }
}
