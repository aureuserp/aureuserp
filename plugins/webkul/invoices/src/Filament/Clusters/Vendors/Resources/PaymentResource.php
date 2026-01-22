<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\PaymentResource as BasePaymentResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\CreatePayment;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\EditPayment;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\ListPayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\ManageBills;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\ViewPayment;
use Webkul\Invoice\Models\Payment;

class PaymentResource extends BasePaymentResource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = true;

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Vendors::class;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/payment.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/payment.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPayment::class,
            EditPayment::class,
            ManageBills::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'view'   => ViewPayment::route('/{record}'),
            'edit'   => EditPayment::route('/{record}/edit'),
            'bills'  => ManageBills::route('/{record}/bills'),
        ];
    }
}
