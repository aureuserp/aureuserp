<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetSourceType;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Settings\AssetSettings;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('assets');
});

it('exposes translated labels for every enum', function () {
    expect(AssetCondition::New->getLabel())->toBe('New')
        ->and(AssetCondition::Damaged->getLabel())->toBe('Damaged')
        ->and(AssetStatus::Held->getLabel())->toBe('In Use')
        ->and(MovementType::Registered->getLabel())->toBe('Registered')
        ->and(MovementType::Issued->getLabel())->toBe('Issued')
        ->and(MovementType::Transferred->getLabel())->toBe('Transferred')
        ->and(MovementType::Returned->getLabel())->toBe('Returned')
        ->and(MovementType::Scrapped->getLabel())->toBe('Scrapped')
        ->and(MovementType::Lost->getLabel())->toBe('Lost')
        ->and(AssetSourceType::Manual->getLabel())->toBe('Manual Entry')
        ->and(AssetSourceType::Bulk->getLabel())->toBe('Bulk Registration')
        ->and(AssetSourceType::Purchase->getLabel())->toBe('Purchase Order');
});

it('keeps stable persisted enum values', function () {
    expect(AssetStatus::Available->value)->toBe('available')
        ->and(AssetStatus::Scrapped->value)->toBe('scrapped')
        ->and(AssetCondition::Poor->value)->toBe('poor')
        ->and(MovementType::Registered->value)->toBe('registered')
        ->and(MovementType::Issued->value)->toBe('issued')
        ->and(MovementType::Transferred->value)->toBe('transferred')
        ->and(MovementType::Returned->value)->toBe('returned')
        ->and(MovementType::Scrapped->value)->toBe('scrapped')
        ->and(MovementType::Lost->value)->toBe('lost')
        ->and(AssetSourceType::Manual->value)->toBe('manual')
        ->and(AssetSourceType::Bulk->value)->toBe('bulk')
        ->and(AssetSourceType::Purchase->value)->toBe('purchase');
});

it('resolves the company asset settings defaults', function () {
    $settings = app(AssetSettings::class);

    expect($settings->tag_prefix)->toBe('AST-')
        ->and($settings->auto_generate_tag)->toBeTrue()
        ->and($settings->tag_padding)->toBe(5)
        ->and($settings->default_condition)->toBe('new')
        ->and($settings->require_serial_by_default)->toBeFalse()
        ->and($settings->default_warehouse_id)->toBeNull()
        ->and($settings->enable_site_assets)->toBeTrue()
        ->and($settings->require_site_on_custody)->toBeFalse()
        ->and($settings->default_custody_duration_days)->toBeNull()
        ->and($settings->require_expected_return_date)->toBeFalse()
        ->and($settings->prevent_duplicate_product_custody)->toBeFalse()
        ->and($settings->require_attachments_on_custody)->toBeFalse()
        ->and($settings->require_attachments_on_damage)->toBeTrue()
        ->and($settings->require_attachments_on_termination)->toBeFalse()
        ->and($settings->restrict_termination_to_managers)->toBeFalse()
        ->and($settings->termination_approval_cost_threshold)->toBeNull()
        ->and(AssetSettings::group())->toBe('assets');
});
