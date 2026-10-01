<?php

use Webkul\Barcode\Livewire\Adjustments;
use Webkul\Barcode\Support\Barcode;

it('normalizes scanned barcodes consistently', function () {
    expect(Barcode::normalize('  WH/OUT/00008  '))->toBe('WH/OUT/00008')
        ->and(Barcode::normalize("#WH/OUT/00008\n"))->toBe('WH/OUT/00008')
        ->and(Barcode::normalize('Packing Slip WH/OUT/00008'))->toBe('WH/OUT/00008')
        ->and(Barcode::normalize("WH   OUT\t00008"))->toBe('WH OUT 00008')
        ->and(Barcode::normalize(null))->toBe('');
});

it('matches barcodes case insensitively after normalization', function () {
    expect(Barcode::matches('WH/OUT/00008', ' wh/out/00008 '))->toBeTrue()
        ->and(Barcode::matches('WH/OUT/00008', 'WH/OUT/00009'))->toBeFalse()
        ->and(Barcode::matches(null, 'WH/OUT/00008'))->toBeFalse();
});

it('accumulates the counted quantity instead of replacing it', function () {
    $component = new Adjustments;
    $component->editingQuantityId = 1;
    $component->editingCountedQuantity = 0.0;

    $component->adjustEditingQuantity(1);
    $component->adjustEditingQuantity(1);
    $component->adjustEditingQuantity(1);

    expect((float) $component->editingCountedQuantity)->toBe(3.0);

    $component->adjustEditingQuantity(-1);

    expect((float) $component->editingCountedQuantity)->toBe(2.0);
});

it('never lets the counted quantity go below zero', function () {
    $component = new Adjustments;
    $component->editingQuantityId = 1;
    $component->editingCountedQuantity = 2.0;

    $component->adjustEditingQuantity(-5);

    expect((float) $component->editingCountedQuantity)->toBe(0.0);

    $component->setEditingQuantity(-3);

    expect((float) $component->editingCountedQuantity)->toBe(0.0);
});

it('ignores quantity changes when no row is being edited', function () {
    $component = new Adjustments;
    $component->editingQuantityId = null;
    $component->editingCountedQuantity = 7.0;

    $component->adjustEditingQuantity(1);
    $component->setEditingQuantity(99);

    expect((float) $component->editingCountedQuantity)->toBe(7.0);
});

it('formats quantities and signed differences for the notices', function () {
    $component = new Adjustments;

    expect($component->formatQuantity(6.0))->toBe('6')
        ->and($component->formatQuantity(6.5))->toBe('6.5')
        ->and($component->formatDifference(2.0))->toBe('+2')
        ->and($component->formatDifference(-2.0))->toBe('-2')
        ->and($component->formatDifference(0.0))->toBe('0');
});
