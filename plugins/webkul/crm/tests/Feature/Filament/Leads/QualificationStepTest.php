<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Pages\ManageSettings;
use Webkul\Crm\Filament\Clusters\Leads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Settings\CrmSettings;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

afterEach(function () {
    $settings = app(CrmSettings::class);
    $settings->use_leads = true;
    $settings->save();
});

function setQualificationStep(bool $enabled): void
{
    $settings = app(CrmSettings::class);
    $settings->use_leads = $enabled;
    $settings->save();
}

it('shows the leads toggle on the settings page and saves it', function () {
    CrmTestHelper::actingAs(['page_crm_manage_settings']);

    Livewire::test(ManageSettings::class)
        ->assertFormFieldExists('use_leads')
        ->fillForm(['use_leads' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(CrmSettings::class)->refresh()->use_leads)->toBeFalse();
});

it('shows the leads menu and list when the qualification step is on', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    setQualificationStep(true);

    expect(LeadResource::canViewAny())->toBeTrue()
        ->and(LeadResource::canCreate())->toBeTrue()
        ->and(Leads::shouldRegisterNavigation())->toBeTrue();

    Livewire::test(ListLeads::class)->assertOk();
});

it('hides the leads menu and blocks the list and create when the qualification step is off', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    setQualificationStep(false);

    expect(LeadResource::canViewAny())->toBeFalse()
        ->and(LeadResource::canCreate())->toBeFalse()
        ->and(Leads::shouldRegisterNavigation())->toBeFalse();

    Livewire::test(ListLeads::class)->assertForbidden();
});

it('still opens an existing lead when the qualification step is off', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $lead = CrmTestHelper::lead();

    setQualificationStep(false);

    Livewire::test(ViewLead::class, ['record' => $lead->getRouteKey()])->assertOk();
});
