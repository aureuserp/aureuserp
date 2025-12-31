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

            if (! in_array(
                needle: $entity,
                haystack: [
                    \Webkul\Security\Filament\Resources\RoleResource::class,
                ],
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
             * Purchase Resources/Pages
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
             * Sale Resources/Pages
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
             * Invoice Resources/Pages
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
             * Accounting Resources/Pages
             */
            \Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource::class,
            \Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalItemResource::class,
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
            \Webkul\Accounting\Filament\Pages\Overview::class,
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
             * Inventory Plugins Resources/Pages
             */
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource::class,
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource::class,
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource::class,
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource::class,
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributeResource::class,
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource::class,
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource::class,
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource::class,
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource::class,
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource::class,
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource::class,
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource::class,
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource::class,
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource::class,
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource::class,
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource::class,
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource::class,
            \Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource::class,
            \Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource::class,
            \Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource::class,
            \Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageLogistics::class,
            \Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageOperations::class,
            \Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageProducts::class,
            \Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageTraceability::class,
            \Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageWarehouses::class,

            /**
             * Project Plugins Resources/Pages
             */
            \Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class,
            \Webkul\Project\Filament\Clusters\Configurations\Resources\MilestoneResource::class,
            \Webkul\Project\Filament\Clusters\Configurations\Resources\ProjectStageResource::class,
            \Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource::class,
            \Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource::class,
            \Webkul\Project\Filament\Clusters\Settings\Pages\ManageTasks::class,
            \Webkul\Project\Filament\Clusters\Settings\Pages\ManageTime::class,
            \Webkul\Project\Filament\Pages\Dashboard::class,
            \Webkul\Project\Filament\Resources\ProjectResource::class,
            \Webkul\Project\Filament\Resources\TaskResource::class,
            \Webkul\Project\Filament\Widgets\StatsOverviewWidget::class,
            \Webkul\Project\Filament\Widgets\TaskByStageChart::class,
            \Webkul\Project\Filament\Widgets\TaskByStateChart::class,
            \Webkul\Project\Filament\Widgets\TopAssigneesWidget::class,
            \Webkul\Project\Filament\Widgets\TopProjectsWidget::class,

            /**
             * Timesheet Resources/Pages
             */
            \Webkul\Timesheet\Filament\Resources\TimesheetResource::class,

            /**
             * Employee Resources/Pages
             */
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class,
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource::class,
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource::class,
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategoryResource::class,
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypeResource::class,
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositionResource::class,
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource::class,
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource::class,
            \Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkillResource::class,
            \Webkul\Employee\Filament\Resources\DepartmentResource::class,
            \Webkul\Employee\Filament\Resources\EmployeeResource::class,

            /**
             * Recruitment Resources/Pages
             */
            \Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource::class,
            \Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource::class,
            \Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositionResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypeResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategoryResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DegreeResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\EmploymentTypeResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositionResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasonResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypeResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediumResource::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSourceResource::class,
            \Webkul\Recruitment\Filament\Pages\Recruitments::class,
            \Webkul\Recruitment\Filament\Widgets\ApplicantChartWidget::class,
            \Webkul\Recruitment\Filament\Widgets\JobPositionStatsWidget::class,

            /**
             * TimeOff Resources/Pages
             */
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource::class,
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\ActivityTypeResource::class,
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource::class,
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDayResource::class,
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidayResource::class,
            \Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource::class,
            \Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource::class,
            \Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource::class,
            \Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource::class,
            \Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource::class,
            \Webkul\TimeOff\Filament\Pages\ByType::class,
            \Webkul\TimeOff\Filament\Pages\Dashboard::class,
            \Webkul\TimeOff\Filament\Pages\Overview::class,
            \Webkul\TimeOff\Filament\Widgets\CalendarWidget::class,
            \Webkul\TimeOff\Filament\Widgets\LeaveTypeWidget::class,
            \Webkul\TimeOff\Filament\Widgets\MyTimeOffWidget::class,
            \Webkul\TimeOff\Filament\Widgets\OverviewCalendarWidget::class,

            /**
             * Website Resources/Pages
             */
            \Webkul\Website\Filament\Admin\Clusters\Settings\Pages\ManageContacts::class,
            \Webkul\Website\Filament\Admin\Resources\PageResource::class,
            \Webkul\Website\Filament\Admin\Resources\PartnerResource::class,

            /**
             * Blog Plugins Resources/Pages
             */
            \Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource::class,
            \Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource::class,
            \Webkul\Blog\Filament\Admin\Resources\PostResource::class,

            /**
             * Contact Plugins Resources/Pages
             */
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccountResource::class,
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\BankResource::class,
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\IndustryResource::class,
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\TagResource::class,
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\TitleResource::class,
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccountResource::class,
            \Webkul\Contact\Filament\Resources\AddressResource::class,
            \Webkul\Contact\Filament\Resources\PartnerResource::class,

            /**
             * Field Resources/Pages
             */
            \Webkul\Field\Filament\Resources\FieldResource::class,

            /**
             * PluginManager Resources/Pages
             */
            \Webkul\PluginManager\Filament\Resources\PluginResource::class,

            /**
             * Security Resources/Pages
             */
            \Webkul\Security\Filament\Resources\CompanyResource::class,
            // \Webkul\Security\Filament\Resources\RoleResource::class,
            \Webkul\Security\Filament\Resources\TeamResource::class,
            \Webkul\Security\Filament\Resources\UserResource::class,
            \Webkul\Security\Filament\Clusters\Settings\Pages\ManageActivity::class,
            \Webkul\Security\Filament\Clusters\Settings\Pages\ManageCurrency::class,
            \Webkul\Security\Filament\Clusters\Settings\Pages\ManageUsers::class,

            /**
             * Support Resources/Pages
             */
            \Webkul\Support\Filament\Pages\Profile::class,
        ];
    }
}
