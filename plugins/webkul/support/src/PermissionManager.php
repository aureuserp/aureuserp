<?php

namespace Webkul\Support;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Str;

class PermissionManager
{
    public function managePermissions(): void
    {
        FilamentShield::buildPermissionKeyUsing(function (string $entity, string $affix, string $subject): string {
            $affix = Str::snake($affix);

            if (
                $entity === 'BezhanSalleh\FilamentShield\Resources\Roles\RoleResource'
                || $entity === 'App\Filament\Resources\RoleResource'
            ) {
                return $affix.'_role';
            }

            if (
                class_exists($entity)
                && method_exists($entity, 'getModel')
            ) {
                $resourceIdentifier = Str::of($entity)
                    ->afterLast('Resources\\')
                    ->beforeLast('Resource')
                    ->replace('\\', '')
                    ->snake()
                    ->replace('_', '::')
                    ->toString();

                if (in_array(
                    needle: $entity,
                    haystack: $this->getConflictingResources(),
                    strict: true
                )) {
                    $pluginPrefix = '';

                    if (Str::contains($entity, 'Webkul\\')) {
                        $pluginPrefix = Str::of($entity)
                            ->after('Webkul\\')
                            ->before('\\')
                            ->snake()
                            ->toString();
                    }

                    if ($pluginPrefix) {
                        return "{$affix}_{$pluginPrefix}_{$resourceIdentifier}";
                    }
                }

                return "{$affix}_{$resourceIdentifier}";
            }

            if (Str::contains($entity, 'Pages\\')) {
                return 'page_'.Str::snake(class_basename($entity));
            }

            if (
                Str::contains($entity, 'Widgets\\')
                || Str::endsWith($entity, 'Widget')
            ) {
                return 'widget_'.Str::snake(class_basename($entity));
            }

            return $affix.'_'.Str::snake($subject);
        });
    }

    protected function getConflictingResources(): array
    {
        return [
            /**
             * Purchase Resources
             */
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource::class,

            /**
             * Sale Resources
             */
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource::class,
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypeResource::class,
            \Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource::class,
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\TagResource::class,
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource::class,

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
