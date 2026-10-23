<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\PartnerLevelResource\Pages\ManagePartnerLevels;
use Webkul\Crm\Models\CrmPartner;
use Webkul\Crm\Models\PartnerLevel;
use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

it('has the default Gold, Silver and Bronze levels', function () {
    expect(PartnerLevel::query()->orderBy('sort')->pluck('name')->take(3)->all())->toBe(['Gold', 'Silver', 'Bronze']);
});

it('lists, creates and edits levels with the level permission', function () {
    CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['partner::level']));

    $gold = PartnerLevel::query()->where('name', 'Gold')->firstOrFail();

    Livewire::test(ManagePartnerLevels::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$gold])
        ->callAction('create', data: ['name' => 'Platinum', 'partner_weight' => 5])
        ->assertHasNoActionErrors()
        ->callTableAction('edit', $gold, data: ['name' => 'Gold', 'partner_weight' => 0])
        ->assertHasNoTableActionErrors();

    expect(PartnerLevel::query()->where('name', 'Platinum')->value('partner_weight'))->toBe(5)
        ->and($gold->fresh()->partner_weight)->toBe(0);
});

it('rejects a negative level weight', function () {
    CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['partner::level']));

    Livewire::test(ManagePartnerLevels::class)
        ->callAction('create', data: ['name' => 'Broken', 'partner_weight' => -1])
        ->assertHasActionErrors(['partner_weight']);
});

it('is forbidden without the level permission', function () {
    CrmTestHelper::actingAs([]);

    Livewire::test(ManagePartnerLevels::class)->assertForbidden();
});

it('uses the contact weight when set, otherwise the level weight', function () {
    CrmTestHelper::actingAs();

    $silver = PartnerLevel::query()->where('name', 'Silver')->firstOrFail();
    $partner = Partner::factory()->create(['company_id' => CrmTestHelper::companyId()]);

    $crmPartner = CrmPartner::query()->create(['partner_id' => $partner->id, 'level_id' => $silver->id]);

    expect($crmPartner->effectiveWeight())->toBe($silver->partner_weight)
        ->and($partner->crmPartner->level->name)->toBe('Silver');

    $crmPartner->update(['level_weight' => 7]);

    expect($crmPartner->fresh()->effectiveWeight())->toBe(7);
});
