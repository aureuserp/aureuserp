<?php

use Illuminate\Support\Facades\DB;
use Webkul\Account\Models\Category;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Product;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    SecurityHelper::disableUserEvents();

    $this->companyA = Company::query()->firstOrFail();
    $this->companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser([$this->companyA, $this->companyB], activeIds: [$this->companyA->id]);

    $this->accountA = AccountHelper::account('income');
    $this->accountB = AccountHelper::account('income');

    $this->sharedProduct = fn (array $overrides = []) => AccountHelper::product(array_merge([
        'company_id' => null,
    ], $overrides));

    $this->category = fn (array $overrides = []) => Category::query()->create(array_merge([
        'name' => 'Company dependent '.uniqid(),
    ], $overrides));
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('keeps a separate product account per company', function () {
    $product = ($this->sharedProduct)();

    CompanyHelper::setActive([$this->companyA->id]);
    $product->property_account_income_id = $this->accountA->id;
    $product->save();

    CompanyHelper::setActive([$this->companyB->id]);
    $product = Product::query()->findOrFail($product->id);
    $product->property_account_income_id = $this->accountB->id;
    $product->save();

    CompanyHelper::setActive([$this->companyA->id]);
    expect(Product::query()->findOrFail($product->id)->property_account_income_id)->toBe($this->accountA->id);

    CompanyHelper::setActive([$this->companyB->id]);
    expect(Product::query()->findOrFail($product->id)->property_account_income_id)->toBe($this->accountB->id);
});

it('keeps a separate partner account per company', function () {
    $partner = Partner::factory()->create();

    CompanyHelper::setActive([$this->companyA->id]);
    $partner->property_account_receivable_id = $this->accountA->id;
    $partner->save();

    CompanyHelper::setActive([$this->companyB->id]);
    $partner = Partner::query()->findOrFail($partner->id);
    $partner->property_account_receivable_id = $this->accountB->id;
    $partner->save();

    CompanyHelper::setActive([$this->companyA->id]);
    expect(Partner::query()->findOrFail($partner->id)->property_account_receivable_id)->toBe($this->accountA->id);

    CompanyHelper::setActive([$this->companyB->id]);
    expect(Partner::query()->findOrFail($partner->id)->property_account_receivable_id)->toBe($this->accountB->id);
});

it('keeps a separate category account per company', function () {
    $category = ($this->category)();

    CompanyHelper::setActive([$this->companyA->id]);
    $category->property_account_income_id = $this->accountA->id;
    $category->save();

    CompanyHelper::setActive([$this->companyB->id]);
    $category = Category::query()->findOrFail($category->id);
    $category->property_account_income_id = $this->accountB->id;
    $category->save();

    CompanyHelper::setActive([$this->companyA->id]);
    expect(Category::query()->findOrFail($category->id)->property_account_income_id)->toBe($this->accountA->id);

    CompanyHelper::setActive([$this->companyB->id]);
    expect(Category::query()->findOrFail($category->id)->property_account_income_id)->toBe($this->accountB->id);
});

it('does not leak a company account into a company that has none', function () {
    $product = ($this->sharedProduct)();

    CompanyHelper::setActive([$this->companyA->id]);
    $product->property_account_income_id = $this->accountA->id;
    $product->save();

    CompanyHelper::setActive([$this->companyB->id]);

    expect(Product::query()->findOrFail($product->id)->property_account_income_id)->toBeNull();
});

it('reads the same product account from the base and the accounts model class', function () {
    $product = ($this->sharedProduct)();

    $product->property_account_income_id = $this->accountA->id;
    $product->save();

    expect(Webkul\Product\Models\Product::query()->findOrFail($product->id)->property_account_income_id)
        ->toBe($this->accountA->id)
        ->and(Product::query()->findOrFail($product->id)->property_account_income_id)
        ->toBe($this->accountA->id);
});

it('reads the same partner account from the base and the accounts model class', function () {
    $partner = Partner::factory()->create();

    $partner->property_account_receivable_id = $this->accountA->id;
    $partner->save();

    expect(Webkul\Partner\Models\Partner::query()->findOrFail($partner->id)->property_account_receivable_id)
        ->toBe($this->accountA->id)
        ->and(Partner::query()->findOrFail($partner->id)->property_account_receivable_id)
        ->toBe($this->accountA->id);
});

it('reads the same category account from the base and the accounts model class', function () {
    $category = ($this->category)();

    $category->property_account_income_id = $this->accountA->id;
    $category->save();

    expect(Webkul\Product\Models\Category::query()->findOrFail($category->id)->property_account_income_id)
        ->toBe($this->accountA->id)
        ->and(Category::query()->findOrFail($category->id)->property_account_income_id)
        ->toBe($this->accountA->id);
});

it('resolves the partner account through the belongs to relation', function () {
    $partner = Partner::factory()->create();

    $partner->property_account_receivable_id = $this->accountA->id;
    $partner->save();

    expect(Partner::query()->findOrFail($partner->id)->propertyAccountReceivable?->id)->toBe($this->accountA->id)
        ->and(Webkul\Partner\Models\Partner::query()->findOrFail($partner->id)->propertyAccountReceivable?->id)
        ->toBe($this->accountA->id);
});

it('serialises the resolved account rather than the stored column', function () {
    $product = ($this->sharedProduct)();

    $product->property_account_income_id = $this->accountA->id;
    $product->save();

    expect(Product::query()->findOrFail($product->id)->toArray())
        ->toHaveKey('property_account_income_id', $this->accountA->id);
});

it('keeps the value on the record itself when no company is active', function () {
    $product = ($this->sharedProduct)();

    CompanyHelper::withoutCompanyContext(function () use ($product) {
        $product->property_account_income_id = $this->accountA->id;
        $product->save();
    });

    expect(DB::table('products_products')->where('id', $product->id)->value('property_account_income_id'))
        ->toBe($this->accountA->id)
        ->and(DB::table('products_product_company_accounts')->where('product_id', $product->id)->count())
        ->toBe(0);
});

it('falls back to the value on the record for a company without its own value', function () {
    $product = ($this->sharedProduct)();

    CompanyHelper::withoutCompanyContext(function () use ($product) {
        $product->property_account_income_id = $this->accountA->id;
        $product->save();
    });

    CompanyHelper::setActive([$this->companyB->id]);

    expect(Product::query()->findOrFail($product->id)->property_account_income_id)->toBe($this->accountA->id);
});

it('prefers the company value over the value on the record', function () {
    $product = ($this->sharedProduct)();

    CompanyHelper::withoutCompanyContext(function () use ($product) {
        $product->property_account_income_id = $this->accountA->id;
        $product->save();
    });

    CompanyHelper::setActive([$this->companyB->id]);
    $product = Product::query()->findOrFail($product->id);
    $product->property_account_income_id = $this->accountB->id;
    $product->save();

    expect(Product::query()->findOrFail($product->id)->property_account_income_id)->toBe($this->accountB->id);

    CompanyHelper::setActive([$this->companyA->id]);

    expect(Product::query()->findOrFail($product->id)->property_account_income_id)->toBe($this->accountA->id);
});

it('resolves the account of a parent category when the child has none', function () {
    $parent = ($this->category)();
    $child = ($this->category)(['parent_id' => $parent->id]);

    $parent->property_account_income_id = $this->accountA->id;
    $parent->save();

    $product = ($this->sharedProduct)(['category_id' => $child->id]);

    expect(Category::query()->findOrFail($child->id)->accountFromHierarchy('property_account_income_id', $this->companyA->id))
        ->toBe($this->accountA->id)
        ->and(Product::query()->findOrFail($product->id)->getAccounts($this->companyA->id)['income']?->id)
        ->toBe($this->accountA->id);
});
