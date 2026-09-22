<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Product\Enums\PriceRuleApplyTo;
use Webkul\Product\Enums\PriceRuleBase;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\PriceRuleItem;
use Webkul\Product\Models\Product;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource\Pages\CreatePriceList;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource\Pages\EditPriceList;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource\Pages\ListPriceLists;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    foreach (['products', 'sales'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $settings = app(ProductSettings::class);
    $settings->enable_price_lists = true;
    $settings->save();
});

it('names a single record in the singular', function () {
    expect(PriceListResource::getModelLabel())->toBe('Price List')
        ->and(PriceListResource::getPluralModelLabel())->toBe('Price Lists');
});

it('sorts below products in the cluster navigation', function () {
    expect(PriceListResource::getNavigationSort())->toBeGreaterThan(
        ProductResource::getNavigationSort()
    );
});

it('lists price lists for an authorized user', function () {
    FilamentHelper::actingAs(['view_any_product_price::list']);

    $priceList = PriceList::factory()->create();

    Livewire::test(ListPriceLists::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$priceList])
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('currency.name');
});

it('forbids listing price lists without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListPriceLists::class)->assertForbidden();
});

it('keeps the cluster sidebar on the create page', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'create_product_price::list']);

    $page = Livewire::test(CreatePriceList::class)->assertOk();

    expect($page->instance()->getSubNavigation())->not->toBeEmpty();
});

it('offers view and edit navigation on an existing record', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'view_product_price::list', 'update_product_price::list']);

    $priceList = PriceList::factory()->create();

    $page = Livewire::test(EditPriceList::class, ['record' => $priceList->getRouteKey()])->assertOk();

    expect($page->instance()->getSubNavigation())->toHaveCount(2);
});

it('creates a price list with a fixed price rule', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'create_product_price::list']);

    Livewire::test(CreatePriceList::class)
        ->fillForm([
            'name'        => 'Wholesale',
            'currency_id' => default_currency_id(),
            'is_active'   => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(PriceList::query()->where('name', 'Wholesale')->exists())->toBeTrue();
});

it('renders the rules repeater for a price list that already has rules', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'view_product_price::list', 'update_product_price::list']);

    $priceList = PriceList::factory()->create();

    PriceRuleItem::factory()->create([
        'price_list_id' => $priceList->id,
        'apply_to'      => PriceRuleApplyTo::GLOBAL,
        'type'          => PriceRuleType::FIXED,
        'fixed_price'   => 25,
    ]);

    Livewire::test(EditPriceList::class, ['record' => $priceList->getRouteKey()])
        ->assertOk()
        ->assertFormSet(fn (array $state): bool => $state['items'] !== []);
});

it('adds a rule through the add-rule modal', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'view_product_price::list', 'update_product_price::list']);

    $priceList = PriceList::factory()->create();

    Livewire::test(EditPriceList::class, ['record' => $priceList->getRouteKey()])
        ->callFormComponentAction('items', 'add', data: [
            'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
            'product_id'       => null,
            'type'             => PriceRuleType::FIXED->value,
            'fixed_price'      => 42.5,
            'base'             => PriceRuleBase::LIST_PRICE->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $rule = $priceList->refresh()->items()->first();

    expect($rule)->not->toBeNull()
        ->and((float) $rule->fixed_price)->toBe(42.5)
        ->and($rule->apply_to)->toBe(PriceRuleApplyTo::GLOBAL)
        ->and($rule->display_apply_to)->not->toBeNull();
});

it('records a rule left without a product as covering all products', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'view_product_price::list', 'update_product_price::list']);

    $priceList = PriceList::factory()->create();

    Livewire::test(EditPriceList::class, ['record' => $priceList->getRouteKey()])
        ->callFormComponentAction('items', 'add', data: [
            'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
            'type'             => PriceRuleType::FIXED->value,
            'fixed_price'      => 10,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $rule = $priceList->refresh()->items()->first();

    expect($rule->apply_to)->toBe(PriceRuleApplyTo::GLOBAL)
        ->and($rule->product_id)->toBeNull()
        ->and($rule->display_apply_to)->toBe(PriceRuleApplyTo::PRODUCT->value);
});

it('records a rule scoped to a chosen product', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'view_product_price::list', 'update_product_price::list']);

    $priceList = PriceList::factory()->create();

    $product = Product::factory()->create();

    Livewire::test(EditPriceList::class, ['record' => $priceList->getRouteKey()])
        ->callFormComponentAction('items', 'add', data: [
            'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
            'product_id'       => $product->id,
            'type'             => PriceRuleType::FIXED->value,
            'fixed_price'      => 10,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $rule = $priceList->refresh()->items()->first();

    expect($rule->apply_to)->toBe(PriceRuleApplyTo::PRODUCT)
        ->and($rule->product_id)->toBe($product->id);
});

it('records a category rule and clears any product', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'view_product_price::list', 'update_product_price::list']);

    $priceList = PriceList::factory()->create();

    $category = Category::factory()->create();

    Livewire::test(EditPriceList::class, ['record' => $priceList->getRouteKey()])
        ->callFormComponentAction('items', 'add', data: [
            'display_apply_to' => PriceRuleApplyTo::CATEGORY->value,
            'category_id'      => $category->id,
            'type'             => PriceRuleType::FIXED->value,
            'fixed_price'      => 10,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $rule = $priceList->refresh()->items()->first();

    expect($rule->apply_to)->toBe(PriceRuleApplyTo::CATEGORY)
        ->and($rule->category_id)->toBe($category->id)
        ->and($rule->product_id)->toBeNull()
        ->and($rule->display_apply_to)->toBe(PriceRuleApplyTo::CATEGORY->value);
});

it('keeps a variant rule pinned to its variant across a re-save', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'view_product_price::list', 'update_product_price::list']);

    $priceList = PriceList::factory()->create();

    $template = Product::factory()->create(['is_configurable' => true]);

    $variant = Product::factory()->create(['parent_id' => $template->id]);

    Product::factory()->create(['parent_id' => $template->id]);

    Livewire::test(EditPriceList::class, ['record' => $priceList->getRouteKey()])
        ->callFormComponentAction('items', 'add', data: [
            'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
            'product_id'       => $template->id,
            'variant_id'       => $variant->id,
            'type'             => PriceRuleType::FIXED->value,
            'fixed_price'      => 10,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $rule = $priceList->refresh()->items()->first();

    expect($rule->apply_to)->toBe(PriceRuleApplyTo::VARIANT)
        ->and($rule->product_id)->toBe($variant->id);

    Livewire::test(EditPriceList::class, ['record' => $priceList->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    $rule = $priceList->refresh()->items()->first();

    expect($rule->apply_to)->toBe(PriceRuleApplyTo::VARIANT)
        ->and($rule->product_id)->toBe($variant->id);
});

it('leaves a rule product scoped when the product has a single variant', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'view_product_price::list', 'update_product_price::list']);

    $priceList = PriceList::factory()->create();

    $template = Product::factory()->create(['is_configurable' => true]);

    Product::factory()->create(['parent_id' => $template->id]);

    Livewire::test(EditPriceList::class, ['record' => $priceList->getRouteKey()])
        ->callFormComponentAction('items', 'add', data: [
            'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
            'product_id'       => $template->id,
            'type'             => PriceRuleType::FIXED->value,
            'fixed_price'      => 10,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $rule = $priceList->refresh()->items()->first();

    expect($rule->apply_to)->toBe(PriceRuleApplyTo::PRODUCT)
        ->and($rule->product_id)->toBe($template->id);
});

it('keeps a variant rule pinned when the product has only one variant', function () {
    FilamentHelper::actingAs(['view_any_product_price::list', 'view_product_price::list', 'update_product_price::list']);

    $priceList = PriceList::factory()->create();

    $template = Product::factory()->create(['is_configurable' => true]);

    $variant = Product::factory()->create(['parent_id' => $template->id]);

    PriceRuleItem::factory()->create([
        'price_list_id' => $priceList->id,
        'product_id'    => $variant->id,
        'type'          => PriceRuleType::FIXED,
        'fixed_price'   => 10,
    ]);

    expect($priceList->refresh()->items()->first()->apply_to)->toBe(PriceRuleApplyTo::VARIANT);

    Livewire::test(EditPriceList::class, ['record' => $priceList->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    $rule = $priceList->refresh()->items()->first();

    expect($rule->apply_to)->toBe(PriceRuleApplyTo::VARIANT)
        ->and($rule->product_id)->toBe($variant->id);
});
