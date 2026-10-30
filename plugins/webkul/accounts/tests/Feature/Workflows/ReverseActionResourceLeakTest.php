<?php

use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ReverseAction;
use Webkul\Account\Filament\Resources\RefundResource;

it('resource does not leak from one ReverseAction instance to another', function () {
    $instance1 = ReverseAction::make('action-invoice');
    $instance2 = ReverseAction::make('action-bill');

    $instance1->setResource(CreditNoteResource::class);
    $instance2->setResource(RefundResource::class);

    expect($instance1->getResource())->toBe(CreditNoteResource::class)
        ->and($instance2->getResource())->toBe(RefundResource::class);
});
