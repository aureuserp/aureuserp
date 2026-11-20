<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Leads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Filament\Clusters\Settings\Pages\ManageCRM;
use Webkul\Crm\Settings\CRMSettings;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

afterEach(function () {
    $settings = app(CRMSettings::class);
    $settings->use_leads = true;
    $settings->save();
});

function setQualificationStep(bool $enabled): void
{
    $settings = app(CRMSettings::class);
    $settings->use_leads = $enabled;
    $settings->save();
}

it('shows the leads toggle on the settings page and saves it', function () {
    CRMHelper::actingAs(['page_crm_manage_c_r_m']);

    Livewire::test(ManageCRM::class)
        ->assertFormFieldExists('use_leads')
        ->fillForm(['use_leads' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(CRMSettings::class)->refresh()->use_leads)->toBeFalse();
});

it('shows the leads menu and list when the qualification step is on', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    setQualificationStep(true);

    expect(LeadResource::canViewAny())->toBeTrue()
        ->and(LeadResource::canCreate())->toBeTrue()
        ->and(Leads::shouldRegisterNavigation())->toBeTrue();

    Livewire::test(ListLeads::class)->assertOk();
});

it('hides the leads menu and blocks the list and create when the qualification step is off', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    setQualificationStep(false);

    expect(LeadResource::canViewAny())->toBeFalse()
        ->and(LeadResource::canCreate())->toBeFalse()
        ->and(Leads::shouldRegisterNavigation())->toBeFalse();

    Livewire::test(ListLeads::class)->assertForbidden();
});

it('still opens an existing lead when the qualification step is off', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $lead = CRMHelper::lead();

    setQualificationStep(false);

    Livewire::test(ViewLead::class, ['record' => $lead->getRouteKey()])->assertOk();
});
