<?php

use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\PaymentResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationDeliveryResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ManageDeliveries;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ManageInvoices;
use Webkul\Crm\Livewire\QuotationInvoiceSummary;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

it('keeps quotation invoices, deliveries and payments inside the CRM', function (string $resource) {
    expect($resource::getCluster())->toBe(Sales::class)
        ->and($resource::shouldRegisterNavigation())->toBeFalse();
})->with([
    'invoices'   => QuotationInvoiceResource::class,
    'deliveries' => QuotationDeliveryResource::class,
    'payments'   => PaymentResource::class,
]);

it('nests invoices and deliveries under the CRM quotation', function () {
    expect(QuotationInvoiceResource::getParentResourceRegistration()->getParentResource())->toBe(QuotationResource::class)
        ->and(QuotationDeliveryResource::getParentResourceRegistration()->getParentResource())->toBe(QuotationResource::class)
        ->and(ManageInvoices::getRelatedResource())->toBe(QuotationInvoiceResource::class)
        ->and(ManageDeliveries::getRelatedResource())->toBe(QuotationDeliveryResource::class);
});

it('links invoice payments to the CRM payment page', function () {
    expect(QuotationInvoiceResource::getSummaryComponent())->toBe(QuotationInvoiceSummary::class)
        ->and(PaymentResource::getUrl('view', ['record' => 1]))->toContain('/crm/sales/payments/1')
        ->and(PaymentResource::getIndexUrl())->toBe(QuotationResource::getUrl('index'));
});
