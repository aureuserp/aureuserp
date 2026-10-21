<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages\ListCompanies;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages\ViewCompany;
use Webkul\Crm\Policies\CompanyPolicy;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

it('lists companies with the crm company permission', function () {
    CrmTestHelper::actingAsCrmUser(['company']);

    Livewire::test(ListCompanies::class)
        ->assertCanSeeTableRecords([CrmTestHelper::companyId()]);
});

it('opens the create company page with the crm create permission', function () {
    CrmTestHelper::actingAsCrmUser(['company']);

    Livewire::test(CreateCompany::class)
        ->assertSuccessful();
});

it('opens the view and edit pages of a company with the crm permissions', function () {
    CrmTestHelper::actingAsCrmUser(['company']);

    $company = CompanyResource::getModel()::query()->findOrFail(CrmTestHelper::companyId());

    Livewire::test(ViewCompany::class, ['record' => $company->getRouteKey()])
        ->assertSee($company->name);

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->assertSchemaStateSet(['name' => $company->name]);
});

it('forbids the crm companies with only the security company permissions', function () {
    CrmTestHelper::actingAs([
        'view_any_security_company',
        'view_security_company',
        'create_security_company',
        'update_security_company',
    ]);

    Livewire::test(ListCompanies::class)->assertForbidden();
    Livewire::test(CreateCompany::class)->assertForbidden();
});

it('forbids editing a company with only the security update permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_company', 'view_crm_company', 'update_security_company']);

    Livewire::test(EditCompany::class, ['record' => CrmTestHelper::companyId()])->assertForbidden();
});

it('authorizes the companies resource against the crm company policy', function () {
    expect(CompanyResource::getCrmPolicy())->toBe(CompanyPolicy::class)
        ->and(CompanyResource::getCluster())->toBe(Configurations::class)
        ->and(CompanyResource::canGloballySearch())->toBeFalse();
});

it('takes the companies resource labels from the crm translations', function () {
    expect(CompanyResource::getModelLabel())->toBe(__('crm::filament/clusters/configurations/resources/company.model-label'))
        ->and(CompanyResource::getPluralModelLabel())->toBe(__('crm::filament/clusters/configurations/resources/company.plural-model-label'))
        ->and(CompanyResource::getNavigationLabel())->toBe(__('crm::filament/clusters/configurations/resources/company.navigation.title'))
        ->and(CompanyResource::getNavigationGroup())->toBe(__('crm::filament/clusters/configurations/resources/company.navigation.group'));

    app()->setLocale('ar');

    expect(CompanyResource::getModelLabel())->toBe('شركة')
        ->and(CompanyResource::getPluralModelLabel())->toBe('الشركات');
});
