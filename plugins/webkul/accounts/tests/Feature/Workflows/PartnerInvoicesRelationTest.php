<?php

use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    $this->partner = Partner::factory()->create();
    $this->contact = Partner::factory()->create(['parent_id' => $this->partner->id]);

    $this->move = fn (Partner $partner, MoveType $type) => Move::factory()->create([
        'partner_id' => $partner->id,
        'move_type'  => $type,
    ]);
});

it('lists the partner invoices and credit notes only', function () {
    $invoice = ($this->move)($this->partner, MoveType::OUT_INVOICE);
    $creditNote = ($this->move)($this->partner, MoveType::OUT_REFUND);
    ($this->move)($this->contact, MoveType::OUT_INVOICE);
    ($this->move)(Partner::factory()->create(), MoveType::OUT_INVOICE);
    ($this->move)($this->partner, MoveType::IN_INVOICE);

    expect($this->partner->invoices()->pluck('id')->sort()->values()->all())
        ->toBe(collect([$invoice->id, $creditNote->id])->sort()->values()->all());
});

it('lists the partner bills and vendor refunds only', function () {
    $bill = ($this->move)($this->partner, MoveType::IN_INVOICE);
    $refund = ($this->move)($this->partner, MoveType::IN_REFUND);
    ($this->move)($this->contact, MoveType::IN_INVOICE);
    ($this->move)($this->partner, MoveType::OUT_INVOICE);

    expect($this->partner->bills()->pluck('id')->sort()->values()->all())
        ->toBe(collect([$bill->id, $refund->id])->sort()->values()->all());
});

it('supports eager loading, counting and existence queries on the relation', function () {
    ($this->move)($this->partner, MoveType::OUT_INVOICE);

    $partner = Partner::query()->with('invoices')->withCount('invoices')->has('invoices')->find($this->partner->id);

    expect($partner)->not->toBeNull()
        ->and($partner->invoices_count)->toBe(1)
        ->and($partner->invoices)->toHaveCount(1);
});
