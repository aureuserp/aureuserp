<?php

namespace Webkul\Account\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\PaymentResource;
use Webkul\Account\Filament\Resources\RefundResource;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\PartialReconcile;

class InvoiceSummary extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $record = null;

    public $subtotal = 0;

    public $totalDiscount = 0;

    public $totalTax = 0;

    public $grandTotal = 0;

    public $amountTax = 0;

    public $rounding = 0;

    public $currency = null;

    public $reconcilablePayments = null;

    public $reconciledPayments = null;

    protected $listeners = ['itemUpdated' => 'refreshSummary'];

    public function refreshSummary($totals)
    {
        $this->subtotal = $totals['subtotal'];
        $this->totalTax = $totals['totalTax'];
        $this->grandTotal = $totals['grandTotal'];
        $this->amountTax = $totals['totalTax'];
        $this->rounding = $totals['rounding'];
    }

    public function reconcileAction(): Action
    {
        return Action::make('reconcile')
            ->label('Add')
            ->icon('heroicon-o-check-circle')
            ->size('xs')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $lines = MoveLine::where('id', $arguments['lineId'])->get();

                $lines = $lines->merge($this->record->lines->filter(fn ($line) => $line->account_id == $lines->first()->account_id && ! $line->reconciled
                ));

                AccountFacade::reconcile($lines);
            })
            ->after(fn () => $this->js('window.location.reload()'));
    }

    public function unReconcileAction(): Action
    {
        return Action::make('unReconcile')
            ->label('Unlink')
            ->icon('heroicon-o-x-circle')
            ->size('xs')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $partialReconcile = PartialReconcile::find($arguments['partial_id']);

                AccountFacade::unReconcile($partialReconcile);
            })
            ->after(fn () => $this->js('window.location.reload()'));
    }

    public function getResourceUrl($record): ?string
    {
        return match ($record['move_type']) {
            MoveType::OUT_INVOICE => InvoiceResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::IN_INVOICE  => BillResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::OUT_REFUND  => CreditNoteResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::IN_REFUND   => RefundResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::ENTRY       => PaymentResource::getUrl('view', ['record' => $record['account_payment_id']]),
        };
    }

    public function render()
    {
        $this->reconcilablePayments = $this->record?->getReconcilablePayments();

        $this->reconciledPayments = $this->record?->getReconciledPayments();

        return view('accounts::livewire/invoice-summary');
    }
}
