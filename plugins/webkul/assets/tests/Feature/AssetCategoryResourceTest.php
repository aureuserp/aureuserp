<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

use Filament\Actions\DeleteAction;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages\CreateAssetCategory;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages\EditAssetCategory;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages\ListAssetCategories;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages\ViewAssetCategory;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\RelationManagers\CategoryProductsRelationManager;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Support\Models\Company;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('assets');
});

it('forbids listing asset categories without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListAssetCategories::class)->assertForbidden();
});

it('allows listing asset categories for users holding only product permissions', function () {
    FilamentHelper::actingAs(['view_any_asset_product']);

    $category = AssetCategory::factory()->create(['name' => 'IT Equipment']);

    Livewire::test(ListAssetCategories::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$category])
        ->assertCanRenderTableColumn('products_count')
        ->assertCanRenderTableColumn('assets_count')
        ->assertCanRenderTableColumn('assets_sum_cost');
});

it('lists, searches, and filters asset categories', function () {
    FilamentHelper::actingAs(['view_any_asset_asset::category']);

    $parent = AssetCategory::factory()->create(['name' => 'Office Furniture']);
    $child = AssetCategory::factory()->withParent($parent)->create(['name' => 'Desks']);
    $other = AssetCategory::factory()->create(['name' => 'Vehicles']);

    Livewire::test(ListAssetCategories::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$parent, $child, $other])
        ->searchTable('Desks')
        ->assertCanSeeTableRecords([$child])
        ->assertCanNotSeeTableRecords([$parent, $other])
        ->searchTable(null)
        ->filterTable('parent_id', $parent->id)
        ->assertCanSeeTableRecords([$parent, $child])
        ->assertCanNotSeeTableRecords([$other]);
});

it('creates a nested asset category with a resolved hierarchy', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::category',
        'create_asset_asset::category',
    ]);

    $company = Company::query()->firstOrFail();
    $parent = AssetCategory::factory()->create(['name' => 'Hardware']);

    Livewire::test(CreateAssetCategory::class)
        ->fillForm([
            'name'       => 'Servers',
            'parent_id'  => $parent->id,
            'company_id' => $company->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $category = AssetCategory::query()->where('name', 'Servers')->firstOrFail();

    expect($category->parent_id)->toBe($parent->id)
        ->and($category->full_name)->toBe('Hardware / Servers')
        ->and($category->parent_path)->toBe('/'.$parent->id.'/');
});

it('renders the asset category view page with product and asset counts', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::category',
        'view_asset_asset::category',
    ]);

    $category = AssetCategory::factory()->create(['name' => 'Monitors']);
    $product = AssetProduct::factory()->create(['category_id' => $category->id]);
    Asset::factory()->forProduct($product)->create();

    Livewire::test(ViewAssetCategory::class, ['record' => $category->id])
        ->assertOk()
        ->assertSee('Monitors')
        ->assertSee('Products');

    expect($category->products()->count())->toBe(1)
        ->and($category->assets()->count())->toBe(1);
});

it('renders the asset category edit page', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::category',
        'view_asset_asset::category',
        'update_asset_asset::category',
    ]);

    $category = AssetCategory::factory()->create(['name' => 'Printers']);

    Livewire::test(EditAssetCategory::class, ['record' => $category->id])
        ->assertOk()
        ->assertFormSet([
            'name' => 'Printers',
        ]);
});

it('lists products belonging to a category in the relation manager', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::category',
        'view_asset_asset::category',
        'view_any_asset_product',
        'view_asset_product',
    ]);

    $category = AssetCategory::factory()->create(['name' => 'Laptops']);
    $product = AssetProduct::factory()->create(['category_id' => $category->id]);
    $otherProduct = AssetProduct::factory()->create();

    Livewire::test(CategoryProductsRelationManager::class, [
        'ownerRecord' => $category,
        'pageClass'   => ViewAssetCategory::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords([$product])
        ->assertCanNotSeeTableRecords([$otherProduct]);
});

it('blocks deleting an asset category referenced by children through the table action', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::category',
        'delete_asset_asset::category',
    ]);

    $parent = AssetCategory::factory()->create(['name' => 'IT']);
    AssetCategory::factory()->withParent($parent)->create(['name' => 'Laptops']);

    Livewire::test(ListAssetCategories::class)
        ->callTableAction(DeleteAction::class, $parent)
        ->assertNotified();

    expect(AssetCategory::query()->find($parent->id))->not->toBeNull();
});

it('deletes an unreferenced asset category through the table action', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::category',
        'delete_asset_asset::category',
    ]);

    $category = AssetCategory::factory()->create(['name' => 'Obsolete Line']);

    Livewire::test(ListAssetCategories::class)
        ->callTableAction(DeleteAction::class, $category);

    expect(AssetCategory::query()->find($category->id))->toBeNull();
});

it('prevents circular parent references on asset categories', function () {
    $parent = AssetCategory::factory()->create(['name' => 'Parent Cat']);
    $child = AssetCategory::factory()->withParent($parent)->create(['name' => 'Child Cat']);

    expect(fn () => $parent->update(['parent_id' => $child->id]))
        ->toThrow(ValidationException::class);
});
