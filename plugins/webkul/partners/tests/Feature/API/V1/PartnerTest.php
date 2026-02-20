<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('partners_partners')) {
        $this->artisan('migrate')->assertSuccessful();
    }

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsPartnerApiUser(array $permissions = []): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function partnerPayload(array $overrides = []): array
{
    return Partner::factory()->make(array_merge([
        'account_type' => AccountType::INDIVIDUAL,
        'company_id'   => null,
        'title_id'     => null,
        'industry_id'  => null,
        'user_id'      => null,
        'creator_id'   => null,
    ], $overrides))->toArray();
}

function createPartnerRecord(array $overrides = []): Partner
{
    return Partner::factory()->create(array_merge([
        'account_type' => AccountType::INDIVIDUAL,
        'company_id'   => null,
        'title_id'     => null,
        'industry_id'  => null,
    ], $overrides));
}

function partnerRoute(string $action, mixed $partner = null): string
{
    $name = "admin.api.v1.partners.partners.{$action}";

    return $partner ? route($name, $partner) : route($name);
}

it('requires authentication to list partners', function () {
    $this->getJson(partnerRoute('index'))->assertUnauthorized();
});

it('forbids listing partners without permission', function () {
    actingAsPartnerApiUser();

    $this->getJson(partnerRoute('index'))->assertForbidden();
});

it('lists partners for authorized users', function () {
    actingAsPartnerApiUser(['view_any_partner_partner']);

    $firstPartner = createPartnerRecord();
    $secondPartner = createPartnerRecord();

    $response = $this->getJson(partnerRoute('index'));

    $response->assertOk();

    $returnedIds = collect($response->json('data'))->pluck('id');

    expect($returnedIds)
        ->toContain($firstPartner->id)
        ->toContain($secondPartner->id);
});

it('creates a partner with valid payload', function () {
    actingAsPartnerApiUser(['create_partner_partner']);

    $payload = partnerPayload();

    $this->postJson(partnerRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Partner created successfully.')
        ->assertJsonPath('data.name', $payload['name']);
});

it('shows a partner for authorized users', function () {
    actingAsPartnerApiUser(['view_partner_partner']);

    $partner = createPartnerRecord();

    $this->getJson(partnerRoute('show', $partner))
        ->assertOk()
        ->assertJsonPath('data.id', $partner->id);
});

it('updates a partner for authorized users', function () {
    actingAsPartnerApiUser(['update_partner_partner']);

    $partner = createPartnerRecord();

    $this->patchJson(partnerRoute('update', $partner), ['name' => 'Updated Partner'])
        ->assertOk()
        ->assertJsonPath('message', 'Partner updated successfully.')
        ->assertJsonPath('data.name', 'Updated Partner');
});

it('deletes a partner for authorized users', function () {
    actingAsPartnerApiUser(['delete_partner_partner']);

    $partner = createPartnerRecord();

    $this->deleteJson(partnerRoute('destroy', $partner))
        ->assertOk()
        ->assertJsonPath('message', 'Partner deleted successfully.');

    $this->assertSoftDeleted('partners_partners', ['id' => $partner->id]);
});

it('restores a partner for authorized users', function () {
    actingAsPartnerApiUser(['restore_partner_partner']);

    $partner = createPartnerRecord();
    $partner->delete();

    $this->postJson(partnerRoute('restore', $partner->id))
        ->assertOk()
        ->assertJsonPath('message', 'Partner restored successfully.');
});

it('force deletes a partner for authorized users', function () {
    actingAsPartnerApiUser(['force_delete_partner_partner']);

    $partner = createPartnerRecord();
    $partner->delete();

    $this->deleteJson(partnerRoute('force-destroy', $partner->id))
        ->assertOk()
        ->assertJsonPath('message', 'Partner permanently deleted.');

    $this->assertDatabaseMissing('partners_partners', ['id' => $partner->id]);
});
