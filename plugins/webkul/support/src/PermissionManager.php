<?php

namespace Webkul\Support;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Str;
use Filament\Pages\BasePage as Page;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;

class PermissionManager
{
    public function managePermissions(): void
    {
        FilamentShield::buildPermissionKeyUsing(function (string $entity, string $affix, string $subject, string $case, string $separator) {
            $pluginKey = null;

            if (in_array(
                needle: $entity,
                haystack: $this->getPluginPrefixRequiredEntities(),
                strict: true
            )) {
                $pluginKey = Str::of($entity)
                    ->after('Webkul\\')
                    ->before('\\')
                    ->snake()
                    ->toString();
            }

            return match(true) {
                is_subclass_of($entity, Resource::class) => Str::of($affix)
                    ->snake()
                    ->when($pluginKey, fn ($str) => $str->append('_')->append($pluginKey))
                    ->append('_')
                    ->append(
                        Str::of($entity)
                            ->afterLast('\\')
                            ->beforeLast('Resource')
                            ->replace('\\', '')
                            ->snake()
                            ->replace('_', '::')
                    )
                    ->toString(),
                is_subclass_of($entity, Page::class) => Str::of('page')
                    ->when($pluginKey, fn ($str) => $str->append('_')->append($pluginKey))
                    ->append(class_basename($entity))
                    ->snake()
                    ->toString(),
                is_subclass_of($entity, Widget::class) => Str::of('widget')
                    ->when($pluginKey, fn ($str) => $str->append('_')->append($pluginKey))
                    ->append(class_basename($entity))
                    ->snake()
                    ->toString(),
                default => Str::of($affix)
                    ->snake()
                    ->when($pluginKey, fn ($str) => $str->append('_')->append($pluginKey))
                    ->append('_')
                    ->append($subject)
                    ->snake()
                    ->toString(),
            };
        });
    }

    protected function getPluginPrefixRequiredEntities(): array
    {
        return [
            /**
             * Purchase Resources
             */
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\PackagingResource::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductAttributeResource::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Settings\Pages\ManageOrders::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Settings\Pages\ManageProducts::class,

            /**
             * Sale Resources
             */
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource::class,
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypeResource::class,
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\PackagingResource::class,
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource::class,
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource::class,
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\TagResource::class,
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource::class,
            \Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource::class,
            \Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource::class,
            \Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource::class,
            \Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource::class,
            \Webkul\Sale\Filament\Clusters\Settings\Pages\ManageInvoice::class,
            \Webkul\Sale\Filament\Clusters\Settings\Pages\ManagePricing::class,
            \Webkul\Sale\Filament\Clusters\Settings\Pages\ManageProducts::class,
            \Webkul\Sale\Filament\Clusters\Settings\Pages\ManageQuotationAndOrder::class,
            \Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoiceResource::class,
            \Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToUpsellResource::class,

            /**
             * Invoice Resources
             */
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\BankAccountResource::class,
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource::class,
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource::class,
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource::class,
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource::class,
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource::class,
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncotermResource::class,
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNoteResource::class,
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource::class,
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\InvoiceResource::class,
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentResource::class,
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource::class,
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource::class,
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource::class,
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource::class,
            \Webkul\Invoice\Filament\Clusters\Settings\Pages\Products::class,

            /**
             * Accounting Resources
             */
            \Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\AccountResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\CurrencyResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductAttributeResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxGroupResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\IncotermResource::class,
            \Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource::class,
            \Webkul\Accounting\Filament\Clusters\Customer\Resources\CreditNoteResource::class,
            \Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource::class,
            \Webkul\Accounting\Filament\Clusters\Customer\Resources\InvoiceResource::class,
            \Webkul\Accounting\Filament\Clusters\Customer\Resources\PaymentResource::class,
            \Webkul\Accounting\Filament\Clusters\Customer\Resources\ProductResource::class,
            \Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource::class,
            \Webkul\Accounting\Filament\Clusters\Vendors\Resources\RefundResource::class,
            \Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource::class,
            \Webkul\Accounting\Filament\Pages\Dashboard::class,
            \Webkul\Accounting\Filament\Clusters\Reporting\Pages\AgedPayable::class,
            \Webkul\Accounting\Filament\Clusters\Reporting\Pages\AgedReceivable::class,
            \Webkul\Accounting\Filament\Clusters\Reporting\Pages\BalanceSheet::class,
            \Webkul\Accounting\Filament\Clusters\Reporting\Pages\GeneralLedger::class,
            \Webkul\Accounting\Filament\Clusters\Reporting\Pages\PartnerLedger::class,
            \Webkul\Accounting\Filament\Clusters\Reporting\Pages\ProfitLoss::class,
            \Webkul\Accounting\Filament\Clusters\Reporting\Pages\TrialBalance::class,
            \Webkul\Accounting\Filament\Clusters\Settings\Pages\ManageCustomerInvoice::class,
            \Webkul\Accounting\Filament\Clusters\Settings\Pages\ManageDefaultAccounts::class,
            \Webkul\Accounting\Filament\Clusters\Settings\Pages\ManageProducts::class,
            \Webkul\Accounting\Filament\Clusters\Settings\Pages\ManageTaxes::class,


            /**
             * Other Plugins Resources
             */
            \Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource::class,
            \Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource::class,

            /**
             * Employee Resources
             */
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class,

            /**
             * Project Resources
             */
            \Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource::class,

            /**
             * Recruitment Resources
             */
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypeResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\EmploymentTypeResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositionResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypeResource::class,

            /**
             * TimeOff Resources
             */
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\ActivityTypeResource::class,

            /**
             * Inventory Resources
             */
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource::class,
            \Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource::class,
        ];
    }
}
