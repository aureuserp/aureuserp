<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ReverseAction;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;
use Webkul\Contact\Filament\Resources\ContactBillResource;
use Webkul\Contact\Filament\Resources\ContactInvoiceResource;
use Webkul\Contact\Filament\Resources\ContactInvoiceResource\Pages\ViewInvoice;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

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

    $this->partner = Partner::factory()->create();

    $this->move = fn (MoveType $type) => Move::factory()->create([
        'partner_id' => $this->partner->id,
        'move_type'  => $type,
    ]);
});

it('denies the nested resources to a user with only the partner permission', function () {
    FilamentHelper::actingAs(['view_any_contact_partner']);

    expect(ContactInvoiceResource::canAccess())->toBeFalse()
        ->and(ContactBillResource::canAccess())->toBeFalse();
});

it('allows the nested resources with both the partner and the invoice permissions', function () {
    FilamentHelper::actingAs(['view_any_contact_partner', 'view_contact_partner', 'view_any_account_invoice', 'view_any_account_bill']);

    expect(ContactInvoiceResource::canAccess())->toBeTrue()
        ->and(ContactBillResource::canAccess())->toBeTrue();
});

it('keeps the move type filter on the nested resources and includes credit notes and refunds', function () {
    $invoice = ($this->move)(MoveType::OUT_INVOICE);
    $creditNote = ($this->move)(MoveType::OUT_REFUND);
    $bill = ($this->move)(MoveType::IN_INVOICE);
    $refund = ($this->move)(MoveType::IN_REFUND);
    $entry = ($this->move)(MoveType::ENTRY);

    expect(ContactInvoiceResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($invoice->id, $creditNote->id)
        ->not->toContain($bill->id, $refund->id, $entry->id)
        ->and(ContactBillResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($bill->id, $refund->id)
        ->not->toContain($invoice->id, $creditNote->id, $entry->id);
});

it('does not list credit notes on the standalone invoice resource', function () {
    $invoice = ($this->move)(MoveType::OUT_INVOICE);
    $creditNote = ($this->move)(MoveType::OUT_REFUND);

    expect(InvoiceResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($invoice->id)
        ->not->toContain($creditNote->id);
});

it('shows the reverse action on an invoice but not on a credit note opened from the partner tab', function () {
    FilamentHelper::actingAs(['view_any_contact_partner', 'view_contact_partner', 'view_any_account_invoice', 'view_account_invoice']);

    $reverseVisible = function (Move $move): bool {
        $page = new ViewInvoice;
        $page->record = $move;

        $header = new ReflectionMethod($page, 'getHeaderActions');

        $action = collect($header->invoke($page))->first(fn ($action) => $action instanceof ReverseAction);

        return $action->record($move)->isVisible();
    };

    $posted = fn (MoveType $type) => tap(($this->move)($type), fn (Move $move) => $move->forceFill(['state' => MoveState::POSTED])->saveQuietly());

    expect($reverseVisible($posted(MoveType::OUT_INVOICE)))->toBeTrue()
        ->and($reverseVisible($posted(MoveType::OUT_REFUND)))->toBeFalse();
});
