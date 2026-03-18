<?php

use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INVENTORY_RULE_JSON_STRUCTURE = [
    'id',
    'name',
    'action',
];

const INVENTORY_RULE_REQUIRED_FIELDS = [
    'name',
    'action',
    'operation_type_id',
    'source_location_id',
    'destination_location_id',
    'route_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryRuleApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryRuleRoute(string $action, mixed $rule = null): string
{
    $name = "admin.api.v1.inventories.rules.{$action}";

    return $rule ? route($name, $rule) : route($name);
}

function inventoryRulePayload(array $overrides = []): array
{
    $source = Location::factory()->create();
    $destination = Location::factory()->create();
    $route = Route::factory()->create();
    $opType = OperationType::factory()->create();
    $company = Company::factory()->create();

    return array_replace_recursive([
        'name'                    => 'Test Rule',
        'action'                  => RuleAction::PULL->value,
        'operation_type_id'       => $opType->id,
        'source_location_id'      => $source->id,
        'destination_location_id' => $destination->id,
        'route_id'                => $route->id,
        'company_id'              => $company->id,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list rules', function () {
    $this->getJson(inventoryRuleRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a rule', function () {
    $this->postJson(inventoryRuleRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a rule', function () {
    $rule = Rule::factory()->create();

    $this->getJson(inventoryRuleRoute('show', $rule))
        ->assertUnauthorized();
});

it('requires authentication to update a rule', function () {
    $rule = Rule::factory()->create();

    $this->patchJson(inventoryRuleRoute('update', $rule), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a rule', function () {
    $rule = Rule::factory()->create();

    $this->deleteJson(inventoryRuleRoute('destroy', $rule))
        ->assertUnauthorized();
});

it('requires authentication to restore a rule', function () {
    $rule = Rule::factory()->create();
    $rule->delete();

    $this->postJson(inventoryRuleRoute('restore', $rule))
        ->assertUnauthorized();
});

it('requires authentication to force-delete a rule', function () {
    $rule = Rule::factory()->create();
    $rule->delete();

    $this->deleteJson(inventoryRuleRoute('force-destroy', $rule))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing rules without permission', function () {
    actingAsInventoryRuleApiUser();

    $this->getJson(inventoryRuleRoute('index'))
        ->assertForbidden();
});

it('forbids creating a rule without permission', function () {
    actingAsInventoryRuleApiUser();

    $this->postJson(inventoryRuleRoute('store'), inventoryRulePayload())
        ->assertForbidden();
});

it('forbids showing a rule without permission', function () {
    actingAsInventoryRuleApiUser();

    $rule = Rule::factory()->create();

    $this->getJson(inventoryRuleRoute('show', $rule))
        ->assertForbidden();
});

it('forbids updating a rule without permission', function () {
    actingAsInventoryRuleApiUser();

    $rule = Rule::factory()->create();

    $this->patchJson(inventoryRuleRoute('update', $rule), [])
        ->assertForbidden();
});

it('forbids deleting a rule without permission', function () {
    actingAsInventoryRuleApiUser();

    $rule = Rule::factory()->create();

    $this->deleteJson(inventoryRuleRoute('destroy', $rule))
        ->assertForbidden();
});

it('forbids restoring a rule without permission', function () {
    actingAsInventoryRuleApiUser();

    $rule = Rule::factory()->create();
    $rule->delete();

    $this->postJson(inventoryRuleRoute('restore', $rule))
        ->assertForbidden();
});

it('forbids force-deleting a rule without permission', function () {
    actingAsInventoryRuleApiUser();

    $rule = Rule::factory()->create();
    $rule->delete();

    $this->deleteJson(inventoryRuleRoute('force-destroy', $rule))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists rules for authorized users', function () {
    actingAsInventoryRuleApiUser(['view_any_inventory_rule']);

    Rule::factory()->count(3)->create();

    $this->getJson(inventoryRuleRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters rules by name', function () {
    actingAsInventoryRuleApiUser(['view_any_inventory_rule']);

    $rule = Rule::factory()->create(['name' => 'UniqueRuleXYZ']);
    Rule::factory()->count(2)->create();

    $response = $this->getJson(inventoryRuleRoute('index').'?filter[name]=UniqueRuleXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($rule->id);
});

it('filters rules by action', function () {
    actingAsInventoryRuleApiUser(['view_any_inventory_rule']);

    $pullRule = Rule::factory()->create(['action' => RuleAction::PULL]);
    Rule::factory()->push()->create();

    $response = $this->getJson(inventoryRuleRoute('index').'?filter[action]=pull')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($pullRule->id);
});

it('excludes soft-deleted rules from default listing', function () {
    actingAsInventoryRuleApiUser(['view_any_inventory_rule']);

    $active = Rule::factory()->create();
    $deleted = Rule::factory()->create();
    $deleted->delete();

    $response = $this->getJson(inventoryRuleRoute('index'))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($active->id)
        ->and($ids)->not->toContain($deleted->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a rule', function () {
    actingAsInventoryRuleApiUser(['create_inventory_rule']);

    $payload = inventoryRulePayload();

    $this->postJson(inventoryRuleRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Rule created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => INVENTORY_RULE_JSON_STRUCTURE]);

    $this->assertDatabaseHas('inventories_rules', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a rule', function (string $field) {
    actingAsInventoryRuleApiUser(['create_inventory_rule']);

    $payload = inventoryRulePayload();
    unset($payload[$field]);

    $this->postJson(inventoryRuleRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(INVENTORY_RULE_REQUIRED_FIELDS);

it('rejects an invalid rule action', function () {
    actingAsInventoryRuleApiUser(['create_inventory_rule']);

    $this->postJson(inventoryRuleRoute('store'), inventoryRulePayload(['action' => 'invalid_action']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['action']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a rule for authorized users', function () {
    actingAsInventoryRuleApiUser(['view_inventory_rule']);

    $rule = Rule::factory()->create();

    $this->getJson(inventoryRuleRoute('show', $rule))
        ->assertOk()
        ->assertJsonPath('data.id', $rule->id)
        ->assertJsonPath('data.name', $rule->name)
        ->assertJsonStructure(['data' => INVENTORY_RULE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent rule', function () {
    actingAsInventoryRuleApiUser(['view_inventory_rule']);

    $this->getJson(inventoryRuleRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a rule', function () {
    actingAsInventoryRuleApiUser(['update_inventory_rule']);

    $rule = Rule::factory()->create();

    $this->patchJson(inventoryRuleRoute('update', $rule), ['name' => 'Updated Rule'])
        ->assertOk()
        ->assertJsonPath('message', 'Rule updated successfully.')
        ->assertJsonPath('data.name', 'Updated Rule');

    $this->assertDatabaseHas('inventories_rules', [
        'id'   => $rule->id,
        'name' => 'Updated Rule',
    ]);
});

it('returns 404 when updating a non-existent rule', function () {
    actingAsInventoryRuleApiUser(['update_inventory_rule']);

    $this->patchJson(inventoryRuleRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('soft deletes a rule', function () {
    actingAsInventoryRuleApiUser(['delete_inventory_rule']);

    $rule = Rule::factory()->create();

    $this->deleteJson(inventoryRuleRoute('destroy', $rule))
        ->assertOk()
        ->assertJsonPath('message', 'Rule deleted successfully.');

    $this->assertSoftDeleted('inventories_rules', ['id' => $rule->id]);
});

it('returns 404 when deleting a non-existent rule', function () {
    actingAsInventoryRuleApiUser(['delete_inventory_rule']);

    $this->deleteJson(inventoryRuleRoute('destroy', 999999))
        ->assertNotFound();
});

// ── Restore ───────────────────────────────────────────────────────────────────

it('restores a soft-deleted rule', function () {
    actingAsInventoryRuleApiUser(['restore_inventory_rule']);

    $rule = Rule::factory()->create();
    $rule->delete();

    $this->postJson(inventoryRuleRoute('restore', $rule))
        ->assertOk()
        ->assertJsonPath('message', 'Rule restored successfully.');

    $this->assertDatabaseHas('inventories_rules', [
        'id'         => $rule->id,
        'deleted_at' => null,
    ]);
});

it('returns 404 when restoring a non-existent rule', function () {
    actingAsInventoryRuleApiUser(['restore_inventory_rule']);

    $this->postJson(inventoryRuleRoute('restore', 999999))
        ->assertNotFound();
});

// ── Force Delete ──────────────────────────────────────────────────────────────

it('permanently deletes a rule', function () {
    actingAsInventoryRuleApiUser(['force_delete_inventory_rule']);

    $rule = Rule::factory()->create();
    $rule->delete();

    $this->deleteJson(inventoryRuleRoute('force-destroy', $rule))
        ->assertOk()
        ->assertJsonPath('message', 'Rule permanently deleted successfully.');

    $this->assertDatabaseMissing('inventories_rules', ['id' => $rule->id]);
});

it('returns 404 when force-deleting a non-existent rule', function () {
    actingAsInventoryRuleApiUser(['force_delete_inventory_rule']);

    $this->deleteJson(inventoryRuleRoute('force-destroy', 999999))
        ->assertNotFound();
});
