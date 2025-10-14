<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shield Resource
    |--------------------------------------------------------------------------
    |
    | Here you may configure the built-in role management resource. You can
    | customize the URL, choose whether to show model paths, group it under
    | a cluster, and decide which permission tabs to display.
    |
    */

    'shield_resource' => [
        'slug'            => 'shield/roles',
        'show_model_path' => true,
        'cluster'         => null,
        'tabs'            => [
            'pages'              => true,
            'widgets'            => true,
            'resources'          => true,
            'custom_permissions' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy
    |--------------------------------------------------------------------------
    |
    | When your application supports teams, Shield will automatically detect
    | and configure the tenant model during setup. This enables tenant-scoped
    | roles and permissions throughout your application.
    |
    */

    'tenant_model' => null,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | This value contains the class name of your user model. This model will
    | be used for role assignments and must implement the HasRoles trait
    | provided by the Spatie\Permission package.
    |
    */

    'auth_provider_model' => 'Webkul\\Security\\Models\\User',

    /*
    |--------------------------------------------------------------------------
    | Super Admin
    |--------------------------------------------------------------------------
    |
    | Here you may define a super admin that has unrestricted access to your
    | application. You can choose to implement this via Laravel's gate system
    | or as a traditional role with all permissions explicitly assigned.
    |
    */

    'super_admin' => [
        'enabled'         => false,
        'name'            => 'super_admin',
        'define_via_gate' => false,
        'intercept_gate'  => 'before',
    ],

    /*
    |--------------------------------------------------------------------------
    | Panel User
    |--------------------------------------------------------------------------
    |
    | When enabled, Shield will create a basic panel user role that can be
    | assigned to users who should have access to your Filament panels but
    | don't need any specific permissions beyond basic authentication.
    |
    */

    'panel_user' => [
        'enabled' => true,
        'name'    => 'Admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Builder
    |--------------------------------------------------------------------------
    |
    | You can customize how permission keys are generated to match your
    | preferred naming convention and organizational standards. Shield uses
    | these settings when creating permission names from your resources.
    |
    | Supported formats: snake, kebab, pascal, camel, upper_snake, lower_snake
    |
    */

    'permissions' => [
        'separator' => '_',
        'case'      => 'lower_snake',
        'generate'  => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Policies
    |--------------------------------------------------------------------------
    |
    | Shield can automatically generate Laravel policies for your resources.
    | When merge is enabled, the methods below will be combined with any
    | resource-specific methods you define in the resources section.
    |
    */

    'policies' => [
        'path'     => app_path('Policies'),
        'merge'    => false,
        'generate' => false,
        'methods'  => [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'restore_any',
            'reorder',
        ],
        'single_parameter_methods' => [
            'view_any',
            'create',
            'delete_any',
            'force_delete_any',
            'restore_any',
            'reorder',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | Shield supports multiple languages out of the box. When enabled, you
    | can provide translated labels for permissions and roles to create a
    | more localized experience for your international users.
    |
    */

    'localization' => [
        'enabled' => false,
        'key'     => 'filament-shield::filament-shield',
    ],

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | Here you can fine-tune permissions for specific Filament resources.
    | Use the 'manage' array to override the default policy methods for
    | individual resources, giving you granular control over permissions.
    |
    */

    'resources' => [
        'subject' => 'model',
        'manage'  => [
            \Webkul\Partner\Filament\Resources\BankAccountResource::class                                                 => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Account\Filament\Resources\PaymentsResource::class                                                    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Account\Filament\Resources\InvoiceResource::class                                                     => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Account\Filament\Resources\CreditNoteResource::class                                                  => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Account\Filament\Resources\BillResource::class                                                        => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Account\Filament\Resources\RefundResource::class                                                      => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Account\Filament\Resources\BankAccountResource::class                                                 => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Account\Filament\Resources\IncoTermResource::class                                                    => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Account\Filament\Resources\PaymentTermResource::class                                                 => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Account\Filament\Resources\TaxGroupResource::class                                                    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Account\Filament\Resources\TaxResource::class                                                         => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Product\Filament\Resources\CategoryResource::class                                                    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Product\Filament\Resources\AttributeResource::class                                                   => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Product\Filament\Resources\PackagingResource::class                                                   => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Security\Filament\Resources\TeamResource::class                                                       => ['view_any', 'view', 'create', 'update', 'delete'],
            \Webkul\Security\Filament\Resources\UserResource::class                                                       => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Field\Filament\Resources\FieldResource::class                                                         => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource::class                                   => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentsResource::class                                  => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotesResource::class                               => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\InvoiceResource::class                                   => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentsResource::class                                   => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource::class                                       => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource::class                                     => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource::class                                     => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\BankAccountResource::class                          => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncoTermResource::class                             => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource::class                          => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource::class                      => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource::class                     => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource::class                             => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource::class                                  => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource::class                                      => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource::class                                          => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource::class                                       => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource::class                            => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource::class                         => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource::class                        => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\TagResource::class                                     => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\PackagingResource::class                               => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource::class                         => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource::class                               => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource::class                              => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource::class                              => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource::class                              => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource::class                              => ['view_any', 'create'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource::class                                 => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource::class                                 => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource::class                                     => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource::class                         => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource::class                          => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource::class                     => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource::class                              => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource::class                   => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource::class                   => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributeResource::class                  => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource::class                         => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource::class                       => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource::class                            => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource::class                                => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource::class                        => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource::class                    => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource::class                               => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource::class                  => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource::class              => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductAttributeResource::class             => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\PackagingResource::class                    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Project\Filament\Clusters\Configurations\Resources\MilestoneResource::class                           => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource::class                                 => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class                        => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Timesheet\Filament\Resources\TimesheetResource::class                                                 => ['view_any', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Website\Filament\Admin\Resources\PageResource::class                                                  => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Website\Filament\Admin\Resources\PartnerResource::class                                               => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource::class                         => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource::class                              => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Blog\Filament\Admin\Resources\PostResource::class                                                     => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Resources\EmployeeResource::class                                                   => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any'],
            \Webkul\Employee\Filament\Resources\DepartmentResource::class                                                 => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkillResource::class                          => ['view_any', 'view'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class                       => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource::class                           => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource::class                    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategoryResource::class                   => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource::class                       => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource::class                          => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypeResource::class                     => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Contact\Filament\Resources\PartnerResource::class                                                     => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\TagResource::class                                 => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\TitleResource::class                               => ['view_any', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\IndustryResource::class                            => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccountResource::class                         => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\BankResource::class                                => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class                    => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategoryResource::class               => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DegreeResource::class                          => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasonResource::class                    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediumResource::class                       => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSourceResource::class                       => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypeResource::class                       => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource::class                      => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource::class                           => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\EmploymentTypeResource::class                  => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositionResource::class                     => ['view_any', 'update'],
            \Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource::class                         => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource::class                         => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource::class                                   => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource::class                                => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource::class                              => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource::class                                 => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource::class                               => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource::class                         => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidayResource::class                       => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDayResource::class                        => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource::class                           => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],

        ],
        'exclude' => [
            \BezhanSalleh\FilamentShield\Resources\Roles\RoleResource::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    |
    | Most Filament pages only require view permissions. Pages listed in the
    | exclude array will be skipped during permission generation and won't
    | appear in your role management interface.
    |
    */

    'pages' => [
        'subject' => 'class',
        'prefix'  => 'view',
        'exclude' => [
            \Filament\Pages\Dashboard::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    |
    | Like pages, widgets typically only need view permissions. Add widgets
    | to the exclude array if you don't want them to appear in your role
    | management interface.
    |
    */

    'widgets' => [
        'subject' => 'class',
        'prefix'  => 'view',
        'exclude' => [
            \Filament\Widgets\AccountWidget::class,
            \Filament\Widgets\FilamentInfoWidget::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Permissions
    |--------------------------------------------------------------------------
    |
    | Sometimes you need permissions that don't map to resources, pages, or
    | widgets. Define any custom permissions here and they'll be available
    | when editing roles in your application.
    |
    */

    'custom_permissions' => [],

    /*
    |--------------------------------------------------------------------------
    | Entity Discovery
    |--------------------------------------------------------------------------
    |
    | By default, Shield only looks for entities in your default Filament
    | panel. Enable these options if you're using multiple panels and want
    | Shield to discover entities across all of them.
    |
    */

    'discovery' => [
        'discover_all_resources' => false,
        'discover_all_widgets'   => false,
        'discover_all_pages'     => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Policy
    |--------------------------------------------------------------------------
    |
    | Shield can automatically register a policy for role management itself.
    | This lets you control who can manage roles using Laravel's built-in
    | authorization system. Requires a RolePolicy class in your app.
    |
    */

    'register_role_policy' => true,

];
