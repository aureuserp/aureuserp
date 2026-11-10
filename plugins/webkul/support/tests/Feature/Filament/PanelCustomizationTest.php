<?php

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Schemas\Schema;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Http\Request;
use Illuminate\Routing\CompiledRouteCollection;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill;
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
use Webkul\Support\Filament\Customization\CustomizablePanelRegistry;
use Webkul\Support\Filament\Customization\PanelCustomizer;
use Webkul\Support\Filament\Resources\CompanyResource as SupportCompanyResource;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

class CustomizationTestPlugin implements Plugin
{
    public function __construct(
        protected string $id,
        protected array $resources,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function register(Panel $panel): void
    {
        $panel->resources($this->resources);
    }

    public function boot(Panel $panel): void {}
}

afterEach(fn () => PanelCustomizer::flush());

function registeredPanel(Panel $panel): Panel
{
    app(PanelRegistry::class)->register($panel);

    return $panel;
}

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

    return registeredPanel(Panel::make()->id('customization'));
}

function panelWithPluginsAfterId(array ...$pluginResources): Panel
{
    $panel = Panel::make()->id('customization');

    foreach ($pluginResources as $index => $resources) {
        $panel->plugin(new CustomizationTestPlugin("customization-test-{$index}", $resources));
    }

    return registeredPanel($panel);
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

it('keeps one cluster entry per component when plugins added after the panel id repeat it', function () {
    $panel = panelWithPluginsAfterId([ReplenishmentResource::class], [ReplenishmentResource::class, UserResource::class]);

    expect($panel->getClusteredComponents(Operations::class))->toBe([ReplenishmentResource::class]);
});

it('excludes and replaces resources contributed by plugins added after the panel id', function () {
    PanelCustomizer::excludeResources('customization', [PluginResource::class]);
    PanelCustomizer::replaceResource('customization', SecurityCompanyResource::class, RoleResource::class);

    $resources = panelWithPluginsAfterId(
        [UserResource::class],
        [PluginResource::class, SecurityCompanyResource::class],
    )->getResources();

    expect($resources)->toContain(UserResource::class, RoleResource::class)
        ->not->toContain(PluginResource::class)
        ->not->toContain(SecurityCompanyResource::class);
});

it('runs host panel callbacks after plugins added after the panel id', function () {
    $seen = [];

    PanelCustomizer::panel('customization', function (Panel $panel) use (&$seen): void {
        $seen = $panel->getResources();
    });

    panelWithPluginsAfterId([UserResource::class]);

    expect($seen)->toContain(UserResource::class);
});

it('customizes every panel through the Filament panel registry', function () {
    expect(app(PanelRegistry::class))->toBeInstanceOf(CustomizablePanelRegistry::class);
});

it('refuses to take over a panel registry that already holds panels', function () {
    $registry = new PanelRegistry;

    $registry->panels['early'] = Panel::make()->id('early');

    CustomizablePanelRegistry::from($registry);
})->throws(LogicException::class);

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

it('keeps the swapped controller in the compiled route cache', function () {
    $route = new Route('GET', 'page-swap-cache/{record}', ['uses' => ViewBill::class.'@__invoke', 'controller' => ViewBill::class]);

    $route->name('page-swap-cache');

    app('router')->getRoutes()->add($route);

    PanelCustomizer::replacePage(ViewBill::class, EditBill::class);

    PanelCustomizer::swapPageRoutes();

    $collection = new RouteCollection;

    $collection->add($route);

    $collection->refreshNameLookups();

    $collection->refreshActionLookups();

    foreach ($collection as $registered) {
        $registered->prepareForSerialization();
    }

    $compiled = $collection->compile();

    $cached = (new CompiledRouteCollection($compiled['compiled'], $compiled['attributes']))->setRouter(app('router'))->setContainer(app());

    $matched = $cached->match(Request::create('/page-swap-cache/1', 'GET'));

    expect($matched->getAction('controller'))->toBe(EditBill::class)
        ->and($matched->getAction('uses'))->toBe(EditBill::class.'@__invoke')
        ->and($cached->getByName('page-swap-cache')->getAction('controller'))->toBe(EditBill::class);
});

it('writes the replacement page into the filament component cache', function () {
    $cachePath = sys_get_temp_dir().'/filament-cache-'.uniqid();

    config(['filament.cache_path' => $cachePath]);

    PanelCustomizer::replacePage(ViewBill::class, EditBill::class);

    $panel = Panel::make()->id('swap-cache');

    PanelCustomizer::apply($panel);

    $panel->cacheComponents();

    $cache = require $panel->getComponentCachePath();

    File::deleteDirectory($cachePath);

    expect($cache['livewireComponents'])->toContain(EditBill::class);
});
