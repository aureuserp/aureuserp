<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\PaymentResource\Pages\EditPayment;
use Webkul\Crm\Filament\Clusters\Sales\Resources\PaymentResource\Pages\ViewPayment;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource as BasePaymentResource;

class PaymentResource extends BasePaymentResource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Sales::class;

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return QuotationResource::getUrl('index', isAbsolute: $isAbsolute, panel: $panel, tenant: $tenant);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPayment::class,
            EditPayment::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'view' => ViewPayment::route('/{record}'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
