<?php

use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccountResource;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\CurrencyResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncotermResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource;
use Webkul\PluginManager\Filament\Resources\PluginResource;
use Webkul\Security\Filament\Resources\CompanyResource as SecurityCompanyResource;
use Webkul\Security\Filament\Resources\RoleResource;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Support\Filament\Customization\PanelCustomizer;
use Webkul\Support\Filament\Resources\CompanyResource as SupportCompanyResource;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

afterEach(fn () => PanelCustomizer::flush());

function customizedPanel(array $resources, int $registrations = 1): Panel
{
    Panel::configureUsing(function (Panel $panel) use ($resources, $registrations): void {
        if ($panel->getId() !== 'customization') {
            return;
        }

        for ($i = 0; $i < $registrations; $i++) {
            $panel->resources($resources);
        }
    });

    return Panel::make()->id('customization');
}

it('applies host schema defaults after package defaults whatever the registration order', function () {
    PanelCustomizer::schema(fn (Schema $schema) => $schema->defaultCurrency('EUR'));

    Schema::configureUsing(fn (Schema $schema) => $schema->defaultCurrency('USD'));

    expect(Schema::make()->getDefaultCurrency())->toBe('EUR');
});

it('applies host table defaults after package defaults whatever the registration order', function () {
    PanelCustomizer::table(fn (Table $table) => $table->defaultCurrency('EUR'));

    Table::configureUsing(fn (Table $table) => $table->defaultCurrency('USD'));

    expect(Table::make(Mockery::mock(HasTable::class))->getDefaultCurrency())->toBe('EUR');
});

it('excludes and replaces resources before the panel registers them', function () {
    PanelCustomizer::excludeResources('customization', [SupportCompanyResource::class, PluginResource::class]);
    PanelCustomizer::replaceResource('customization', SecurityCompanyResource::class, RoleResource::class);

    $resources = customizedPanel([
        SupportCompanyResource::class,
        PluginResource::class,
        SecurityCompanyResource::class,
        UserResource::class,
    ])->getResources();

    expect($resources)->toContain(UserResource::class, RoleResource::class)
        ->not->toContain(SupportCompanyResource::class)
        ->not->toContain(PluginResource::class)
        ->not->toContain(SecurityCompanyResource::class);
});

it('leaves resources of other panels untouched', function () {
    PanelCustomizer::excludeResources('another-panel', [UserResource::class]);

    expect(customizedPanel([UserResource::class])->getResources())->toContain(UserResource::class);
});

it('keeps one cluster entry per component when a plugin registers twice', function () {
    $panel = customizedPanel([ReplenishmentResource::class], registrations: 2);

    expect($panel->getClusteredComponents(Operations::class))->toBe([ReplenishmentResource::class]);
});

it('runs host panel callbacks after the plugins registered', function () {
    PanelCustomizer::panel('customization', fn (Panel $panel) => $panel->resources([RoleResource::class]));

    expect(customizedPanel([UserResource::class])->getResources())->toContain(UserResource::class, RoleResource::class);
});

it('hides a navigation entry and moves another to a host group', function () {
    PanelCustomizer::hideNavigationItems(null, [UserResource::class]);
    PanelCustomizer::navigationGroup(null, RoleResource::class, 'Access');

    $hidden = NavigationItem::make('Users')->key(UserResource::class)->group('Settings');
    $moved = NavigationItem::make('Roles')->key(RoleResource::class)->group('Settings');
    $untouched = NavigationItem::make('Plugins')->key(PluginResource::class)->group('Settings');

    expect($hidden->isVisible())->toBeFalse()
        ->and($moved->getGroup())->toBe('Access')
        ->and($moved->isVisible())->toBeTrue()
        ->and($untouched->getGroup())->toBe('Settings')
        ->and($untouched->isVisible())->toBeTrue();
});

it('resolves a replaced resource so links keep pointing to the host resource', function () {
    PanelCustomizer::replaceResource(null, UserResource::class, RoleResource::class);

    expect(PanelCustomizer::resource(UserResource::class))->toBe(RoleResource::class)
        ->and(PanelCustomizer::resource(PluginResource::class))->toBe(PluginResource::class);
});

it('hides the navigation entries of resources that repeat another plugin entry', function () {
    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $duplicates = [
        TaxResource::class,
        TaxGroupResource::class,
        PaymentTermResource::class,
        IncotermResource::class,
        CurrencyResource::class,
        ProductCategoryResource::class,
        ProductAttributeResource::class,
        Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource::class,
        Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource::class,
        Webkul\Sale\Filament\Clusters\Configuration\Resources\CurrencyResource::class,
        Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource::class,
        Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource::class,
        BankAccountResource::class,
    ];

    $kept = [
        Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource::class,
        Webkul\Accounting\Filament\Clusters\Configuration\Resources\CurrencyResource::class,
    ];

    PanelCustomizer::hideNavigationItems(null, $duplicates);

    $visibility = fn (string $resource): array => array_map(
        fn (NavigationItem $item): bool => $item->isVisible(),
        $resource::getNavigationItems(),
    );

    foreach ($duplicates as $resource) {
        expect($visibility($resource))->not->toBeEmpty()->not->toContain(true);
    }

    foreach ($kept as $resource) {
        expect($visibility($resource))->toContain(true);
    }
});
