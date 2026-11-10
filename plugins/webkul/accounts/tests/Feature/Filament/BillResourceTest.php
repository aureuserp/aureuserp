<?php

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\Support\View\ViewManager;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\BillResource\Pages\CreateBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\ListBills;
use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\CancelAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ConfirmAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PayAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ResetToDraftAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ReverseAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\SetAsCheckedAction;
use Webkul\Account\Models\Move;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource as AccountingBillResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages\ViewBill as AccountingViewBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\ViewBill as InvoiceViewBill;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages\ViewBill as QuotationViewBill;
use Webkul\Support\Filament\Customization\PanelCustomizer;
use Webkul\Support\Livewire\PageHeaderActions;

class HostAccountingViewBill extends AccountingViewBill {}

class HostInvoiceViewBill extends InvoiceViewBill {}

class HostEditBill extends EditBill {}

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

it('forbids listing bills without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListBills::class)->assertForbidden();
});

it('lists bills with their key columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_account_bill']);

    Livewire::test(ListBills::class)
        ->assertOk()
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state');
});

it('renders the bill create page', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'create_account_bill']);

    Livewire::test(CreateBill::class)->assertOk();
});

it('creates a draft bill with a number through the create form', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'create_account_bill']);

    $partner = AccountHelper::partner();

    Livewire::test(CreateBill::class)
        ->fillForm([
            'partner_id'   => $partner->id,
            'invoice_date' => now(),
            'journal_id'   => AccountHelper::purchaseJournal()->id,
            'currency_id'  => AccountHelper::currency()->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $bill = Move::query()
        ->where('partner_id', $partner->id)
        ->where('move_type', MoveType::IN_INVOICE)
        ->first();

    expect($bill)->not->toBeNull()
        ->and($bill->name)->not->toBeNull();
});

it('posts a draft bill through the confirm action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, null, null, ['invoice_date' => now()]);
    AccountHelper::productLine($bill, AccountHelper::account('expense'), qty: 2, priceUnit: 100);
    AccountHelper::compute($bill);

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->assertActionExists(ConfirmAction::class)
        ->callAction(ConfirmAction::class);

    expect($bill->refresh()->state)->toBe(MoveState::POSTED);
});

it('cancels a draft bill through the cancel action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, null, null, ['invoice_date' => now()]);

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->callAction(CancelAction::class);

    expect($bill->refresh()->state)->toBe(MoveState::CANCEL);
});

function postedBillRecord(): Move
{
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, null, null, ['invoice_date' => now()]);

    AccountHelper::productLine($bill, AccountHelper::account('expense'), qty: 2, priceUnit: 100);

    return AccountHelper::post($bill);
}

it('reverses a posted bill into a refund through the action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    $bill = postedBillRecord();

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->callAction(ReverseAction::class, data: [
            'reason'     => 'Test reversal',
            'journal_id' => $bill->journal_id,
            'date'       => now(),
        ]);

    expect(
        Move::query()
            ->where('reversed_entry_id', $bill->id)
            ->where('move_type', MoveType::IN_REFUND)
            ->exists()
    )->toBeTrue();
});

it('resets a posted bill to draft through the action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    $bill = postedBillRecord();

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->callAction(ResetToDraftAction::class);

    expect($bill->refresh()->state)->toBe(MoveState::DRAFT);
});

it('marks a posted bill as checked through the action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    $bill = postedBillRecord();

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->callAction(SetAsCheckedAction::class);

    expect($bill->refresh()->checked)->toBeTrue();
});

it('registers a full payment and marks the bill paid through the action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    AccountHelper::bankJournal();

    $bill = postedBillRecord();

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->callAction(PayAction::class);

    expect($bill->refresh()->payment_state)->toBe(PaymentState::PAID);
});

function pageSwapRoutes(): array
{
    return [
        ViewBill::class           => Route::get('/page-swap-check/base/{record}', ViewBill::class)->name('page-swap-check.base'),
        AccountingViewBill::class => Route::get('/page-swap-check/accounting/{record}', AccountingViewBill::class)->name('page-swap-check.accounting'),
        InvoiceViewBill::class    => Route::get('/page-swap-check/invoices/{record}', InvoiceViewBill::class)->name('page-swap-check.invoices'),
        QuotationViewBill::class  => Route::get('/page-swap-check/quotations/{record}', QuotationViewBill::class)->name('page-swap-check.quotations'),
    ];
}

it('serves the host page on the route of a swapped record page', function () {
    $route = Route::get('/page-swap-check/{record}', ViewBill::class);

    PanelCustomizer::replacePage(ViewBill::class, EditBill::class);

    try {
        PanelCustomizer::swapPageRoutes();

        expect($route->getAction('controller'))->toBe(EditBill::class)
            ->and($route->getAction('uses'))->toBe(EditBill::class.'@__invoke');
    } finally {
        PanelCustomizer::flush();
    }
});

it('swaps only the routes of the exact registered pages', function () {
    $routes = pageSwapRoutes();

    PanelCustomizer::replacePage(ViewBill::class, HostInvoiceViewBill::class);

    try {
        PanelCustomizer::swapPageRoutes();

        expect($routes[ViewBill::class]->getAction('controller'))->toBe(HostInvoiceViewBill::class);

        foreach ([AccountingViewBill::class, InvoiceViewBill::class, QuotationViewBill::class] as $page) {
            expect(Str::before($routes[$page]->getAction('controller'), '@'))->toBe($page);
        }
    } finally {
        PanelCustomizer::flush();
    }
});

it('serves a host page on each concrete page registered for it, keeping route and resource', function () {
    $routes = pageSwapRoutes();

    PanelCustomizer::replacePage(AccountingViewBill::class, HostAccountingViewBill::class);
    PanelCustomizer::replacePage(InvoiceViewBill::class, HostInvoiceViewBill::class);

    try {
        PanelCustomizer::swapPageRoutes();

        expect($routes[AccountingViewBill::class]->getAction('controller'))->toBe(HostAccountingViewBill::class)
            ->and($routes[AccountingViewBill::class]->getAction('uses'))->toBe(HostAccountingViewBill::class.'@__invoke')
            ->and($routes[AccountingViewBill::class]->getName())->toBe('page-swap-check.accounting')
            ->and($routes[AccountingViewBill::class]->uri())->toBe('page-swap-check/accounting/{record}')
            ->and($routes[InvoiceViewBill::class]->getAction('controller'))->toBe(HostInvoiceViewBill::class)
            ->and(Str::before($routes[ViewBill::class]->getAction('controller'), '@'))->toBe(ViewBill::class)
            ->and(Str::before($routes[QuotationViewBill::class]->getAction('controller'), '@'))->toBe(QuotationViewBill::class)
            ->and(HostAccountingViewBill::getResource())->toBe(AccountingBillResource::class);
    } finally {
        PanelCustomizer::flush();
    }
});

it('adds host header actions to every page that extends the registered page, once', function () {
    FilamentHelper::bootAdminPanel();

    PanelCustomizer::headerActions(ViewBill::class, fn (?Move $record) => [Action::make('baseHostAction')]);
    PanelCustomizer::headerActions(AccountingViewBill::class, fn (?Move $record) => [Action::make('subclassHostAction')]);

    $actionNames = fn (string $page): array => array_map(
        fn (Action $action): string => $action->getName(),
        PanelCustomizer::headerActionsFor($page, null),
    );

    $render = fn (array $scopes): string => FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER, scopes: $scopes)->toHtml();

    try {
        expect($actionNames(AccountingViewBill::class))->toBe(['baseHostAction', 'subclassHostAction'])
            ->and($actionNames(QuotationViewBill::class))->toBe(['baseHostAction'])
            ->and($actionNames(EditBill::class))->toBe([])
            ->and(substr_count($render([AccountingViewBill::class, AccountingBillResource::class]), 'wire:snapshot'))->toBe(1)
            ->and(substr_count($render([QuotationViewBill::class]), 'wire:snapshot'))->toBe(1)
            ->and($render([EditBill::class, BillResource::class]))->toBe('');
    } finally {
        PanelCustomizer::flush();
    }
});

it('collects host header actions in registration order across a page and its parents', function () {
    PanelCustomizer::headerActions(ViewBill::class, fn (?Move $record) => [Action::make('firstBaseAction')]);
    PanelCustomizer::headerActions(AccountingViewBill::class, fn (?Move $record) => [Action::make('subclassAction')]);
    PanelCustomizer::headerActions(ViewBill::class, fn (?Move $record) => [Action::make('secondBaseAction')]);

    try {
        expect(array_map(fn (Action $action): string => $action->getName(), PanelCustomizer::headerActionsFor(AccountingViewBill::class, null)))
            ->toBe(['firstBaseAction', 'subclassAction', 'secondBaseAction']);
    } finally {
        PanelCustomizer::flush();
    }
});

it('registers the header actions render hook once per panel however often it is customized', function () {
    $hookCount = fn (): int => (fn (): int => count($this->renderHooks[PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER][''] ?? []))
        ->call(app(ViewManager::class));

    PanelCustomizer::flush();

    FilamentHelper::bootAdminPanel();

    $panel = Filament::getPanel('admin');

    PanelCustomizer::apply($panel);

    Filament::bootCurrentPanel();

    expect($hookCount())->toBe(1);

    PanelCustomizer::apply($panel);

    PanelCustomizer::headerActions(ViewBill::class, fn (?Move $record) => [Action::make('lateHostAction')]);

    $panelHookCount = fn (): int => (fn (): int => count($this->renderHooks[PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER][''] ?? []))
        ->call($panel);

    try {
        expect($panelHookCount())->toBe(1)
            ->and($hookCount())->toBe(1)
            ->and(substr_count(FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER, scopes: [ViewBill::class, BillResource::class])->toHtml(), 'wire:snapshot'))->toBe(1);
    } finally {
        PanelCustomizer::flush();
    }
});

it('shows host header actions on a page that extends the registered page', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    PanelCustomizer::headerActions(EditBill::class, fn (?Move $record) => [
        Action::make('authorizeBillPayment')->action(fn (Move $record) => $record->update(['reference' => 'authorized'])),
    ]);

    try {
        $bill = AccountHelper::invoice(MoveType::IN_INVOICE, null, null, ['invoice_date' => now()]);

        Livewire::test(HostEditBill::class, ['record' => $bill->id])
            ->assertOk()
            ->assertSeeLivewire(PageHeaderActions::class);

        Livewire::test(PageHeaderActions::class, ['page' => HostEditBill::class, 'model' => $bill::class, 'recordKey' => $bill->id])
            ->assertActionExists('authorizeBillPayment');
    } finally {
        PanelCustomizer::flush();
    }
});

it('shows host header actions on a page without changing the page', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    PanelCustomizer::headerActions(EditBill::class, fn (?Move $record) => [
        Action::make('authorizeBillPayment')->action(fn (Move $record) => $record->update(['reference' => 'authorized'])),
    ]);

    try {
        $bill = AccountHelper::invoice(MoveType::IN_INVOICE, null, null, ['invoice_date' => now()]);

        Livewire::test(EditBill::class, ['record' => $bill->id])
            ->assertOk()
            ->assertSeeLivewire(PageHeaderActions::class);

        Livewire::test(PageHeaderActions::class, ['page' => EditBill::class, 'model' => $bill::class, 'recordKey' => $bill->id])
            ->assertActionExists('authorizeBillPayment')
            ->callAction('authorizeBillPayment');

        expect($bill->refresh()->reference)->toBe('authorized');
    } finally {
        PanelCustomizer::flush();
    }
});
