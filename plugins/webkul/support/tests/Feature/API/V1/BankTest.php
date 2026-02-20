<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Support\Models\Bank;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

require_once __DIR__.'/../../../Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

const BANK_JSON_STRUCTURE = [
    'id',
    'name',
    'code',
    'email',
    'phone',
    'street1',
    'street2',
    'city',
    'zip',
    'creator_id',
    'created_at',
    'updated_at',
    'deleted_at',
    'state',
    'country',
];

beforeEach(function () {
    if (! Schema::hasTable('banks')) {
        test()->markTestSkipped(
            'Support plugin tables are missing. Install/migrate the support plugin before running this suite.'
        );
    }

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsBankApiUser(array $permissions): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function bankRoute(string $action, mixed $bank = null): string
{
    $name = "admin.api.v1.support.banks.{$action}";

    return $bank ? route($name, $bank) : route($name);
}

it('requires authentication to list banks', function () {
    $this->getJson(bankRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing banks without permission', function () {
    actingAsBankApiUser([]);

    $this->getJson(bankRoute('index'))
        ->assertForbidden();
});

it('lists banks for authorized users', function () {
    actingAsBankApiUser(['view_any_supporbank']);

    Bank::factory()->count(2)->create();

    $this->getJson(bankRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => BANK_JSON_STRUCTURE]]);
});

it('creates a bank with valid payload', function () {
    actingAsBankApiUser(['create_support_bank']);

    $state = State::factory()->create();
    $country = Country::factory()->create();
    $payload = Bank::factory()->make([
        'state_id'   => $state->id,
        'country_id' => $country->id,
    ])->toArray();

    $this->postJson(bankRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Bank created successfully.')
        ->assertJsonPath('data.name', $payload['name']);

    $this->assertDatabaseHas('banks', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a bank', function () {
    actingAsBankApiUser(['create_support_bank']);

    $this->postJson(bankRoute('store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('shows a bank for authorized users', function () {
    actingAsBankApiUser(['view_support_acbank']);

    $bank = Bank::factory()->create();

    $this->getJson(bankRoute('show', $bank))
        ->assertOk()
        ->assertJsonPath('data.id', $bank->id)
        ->assertJsonStructure(['data' => BANK_JSON_STRUCTURE]);
});

it('updates a bank for authorized users', function () {
    actingAsBankApiUser(['update_support_bank']);

    $bank = Bank::factory()->create();
    $updatedName = 'BANK-UPDATED-'.fake()->unique()->numerify('####');

    $this->patchJson(bankRoute('update', $bank), ['name' => $updatedName])
        ->assertOk()
        ->assertJsonPath('message', 'Bank updated successfully.')
        ->assertJsonPath('data.name', $updatedName);

    $this->assertDatabaseHas('banks', [
        'id'   => $bank->id,
        'name' => $updatedName,
    ]);
});

it('deletes a bank for authorized users', function () {
    actingAsBankApiUser(['delete_support_bank']);

    $bank = Bank::factory()->create();

    $this->deleteJson(bankRoute('destroy', $bank))
        ->assertOk()
        ->assertJsonPath('message', 'Bank deleted successfully.');

    $this->assertSoftDeleted('banks', ['id' => $bank->id]);
});

it('restores a soft deleted bank for authorized users', function () {
    actingAsBankApiUser(['restore_supportbank']);

    $bank = Bank::factory()->create();
    $bank->delete();

    $this->postJson(bankRoute('restore', ['id' => $bank->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Bank restored successfully.');

    $this->assertDatabaseHas('banks', [
        'id'         => $bank->id,
        'deleted_at' => null,
    ]);
});

it('force deletes a bank for authorized users', function () {
    actingAsBankApiUser(['force_delete_subank']);

    $bank = Bank::factory()->create();
    $bank->delete();

    $this->deleteJson(bankRoute('force-destroy', ['id' => $bank->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Bank permanently deleted.');

    $this->assertDatabaseMissing('banks', ['id' => $bank->id]);
});
