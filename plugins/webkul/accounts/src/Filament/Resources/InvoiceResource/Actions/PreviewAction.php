<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Livewire\Component;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Move;

class PreviewAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.preview';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('Preview'))
            ->color('gray')
            ->visible(fn(Move $record) => $record->state == MoveState::POSTED->value)
            ->modalHeading(__('Preview Invoice'))
            ->modalWidth(MaxWidth::SevenExtraLarge)
            ->modalIcon('heroicon-s-document-text')
            ->modalContent(function ($record) {
                return view('accounts::invoice/actions/preview.index', ['record' => $record]);
            });
    }
}
