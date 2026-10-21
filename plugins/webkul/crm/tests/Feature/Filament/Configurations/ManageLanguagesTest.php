<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Pages\ManageLanguages;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

it('lists every supported locale with the languages permission', function () {
    CrmTestHelper::actingAs(['page_crm_manage_languages']);

    $locales = config('app.supported_locales');

    expect($locales)->not->toBeEmpty();

    $component = Livewire::test(ManageLanguages::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords(array_keys($locales));

    foreach ($locales as $code => $locale) {
        $component
            ->assertSee($locale['label'] ?? $code)
            ->assertSee($locale['native'] ?? $code);
    }
});

it('lists a locale added to the supported locales config', function () {
    config(['app.supported_locales.xx' => ['label' => 'Testish', 'native' => 'Testisch', 'rtl' => true]]);

    CrmTestHelper::actingAs(['page_crm_manage_languages']);

    Livewire::test(ManageLanguages::class)
        ->assertCanSeeTableRecords(['xx'])
        ->assertSee('Testish')
        ->assertSee('Testisch');
});

it('shows the languages page title from the crm translations', function () {
    CrmTestHelper::actingAs(['page_crm_manage_languages']);

    Livewire::test(ManageLanguages::class)
        ->assertSee(__('crm::filament/clusters/configurations/pages/manage-languages.title'));

    expect(ManageLanguages::getNavigationLabel())->toBe(__('crm::filament/clusters/configurations/pages/manage-languages.navigation.title'))
        ->and(ManageLanguages::getNavigationGroup())->toBe(__('crm::filament/clusters/configurations/pages/manage-languages.navigation.group'))
        ->and(ManageLanguages::getCluster())->toBe(Configurations::class);
});

it('grants access to the languages page only with its permission', function () {
    CrmTestHelper::actingAs(['page_crm_manage_languages']);

    expect(ManageLanguages::canAccess())->toBeTrue();

    CrmTestHelper::actingAs(['view_any_crm_user', 'view_any_crm_company']);

    expect(ManageLanguages::canAccess())->toBeFalse();
});

it('forbids the languages page without its permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_user', 'view_any_crm_company']);

    Livewire::test(ManageLanguages::class)->assertForbidden();
});
