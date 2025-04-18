<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\MoveReversal;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotesResource;
use Webkul\Support\Traits\PDFHandler;

class CreditNoteAction extends Action
{
    use PDFHandler;

    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.credit-note';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/credit-note.title'))
            ->color('gray')
            ->visible(fn (Move $record) => $record->state == MoveState::POSTED)
            ->icon('heroicon-o-receipt-refund')
            ->modalHeading(__('accounts::filament/resources/invoice/actions/credit-note.modal.heading'));

        $this->form(
            function (Form $form) {
                return $form->schema([
                    Forms\Components\Textarea::make('reason')
                        ->label(__('accounts::filament/resources/invoice/actions/credit-note.modal.form.reason'))
                        ->maxLength(245)
                        ->required(),
                    Forms\Components\DatePicker::make('date')
                        ->label(__('accounts::filament/resources/invoice/actions/credit-note.modal.form.date'))
                        ->default(now())
                        ->native(false)
                        ->required(),
                ]);
            }
        );

        $this->action(function (Move $record, array $data, $livewire) {
            $user = Auth::user();

            $creditNote = MoveReversal::create([
                'reason'     => $data['reason'],
                'date'       => $data['date'],
                'company_id' => $record->company_id,
                'creator_id' => $user->id,
            ]);

            $creditNote->moves()->attach($record);

            $move = $this->createMove($creditNote, $record);

            $redirectUrl = CreditNotesResource::getUrl('edit', ['record' => $move->id]);

            $livewire->redirect($redirectUrl, navigate: FilamentView::hasSpaMode());
        });
    }

    private function createMove(MoveReversal $creditNote, Move $record): Move
    {
        $moveData = [
            'company_id'                        => $record->company_id,
            'creator_id'                        => $creditNote->creator_id,
            'partner_id'                        => $record->partner_id,
            'commercial_partner_id'             => $record->commercial_partner_id,
            'partner_shipping_id'               => $record->partner_shipping_id,
            'currency_id'                       => $record->currency_id,
            'reversed_entry_id'                 => $record->id,
            'invoice_user_id'                   => $record->invoice_user_id,
            'reference'                         => "Reversal of: {$record->name}, {$creditNote->reason}",
            'state'                             => MoveState::DRAFT,
            'move_type'                         => MoveType::OUT_REFUND,
            'auto_post'                         => 0,
            'payment_state'                     => PaymentState::NOT_PAID,
            'invoice_partner_display_name'      => $record->invoice_partner_display_name,
            'date'                              => $creditNote->date,
            'invoice_date'                      => $record->invoice_date,
            'invoice_date_due'                  => $record->invoice_date_due,
            'amount_untaxed'                    => $record->amount_untaxed,
            'amount_tax'                        => $record->amount_tax,
            'amount_total'                      => $record->amount_total,
            'amount_residual'                   => $record->amount_total,
            'amount_untaxed_signed'             => -$record->amount_untaxed_signed,
            'amount_untaxed_in_currency_signed' => -$record->amount_untaxed_in_currency_signed,
            'amount_tax_signed'                 => -$record->amount_tax_signed,
            'amount_total_signed'               => -$record->amount_total_signed,
            'amount_total_in_currency_signed'   => -$record->amount_total_in_currency_signed,
            'company_currency_id'               => $record->company_currency_id,
            'amount_residual_signed'            => -$record->amount_total_signed,
            'checked'                           => $record->checked,
            'sort'                              => $record->sort + 1,
        ];

        $newMove = Move::create($moveData);

        $creditNote->newMoves()->attach($newMove->id);

        $this->createProductsLines($newMove, $record);

        $this->createPaymentTermLine($newMove, $record);

        $this->createTaxLines($newMove, $record);

        return $newMove;
    }

    private function createProductsLines(Move $newMove, Move $record): void
    {
        $record->lines->each(function (MoveLine $line) use ($newMove, $record) {
            $newMoveLine = $line->replicate();

            $newMoveLine->display_type = 'product';
            $newMoveLine->parent_state = $record->state;
            $newMoveLine->reference = $record->reference;
            $newMoveLine->move_name = null;
            $newMoveLine->move_id = $newMove->id;
            $newMoveLine->debit = $line->credit;
            $newMoveLine->credit = 0.00;
            $newMoveLine->balance = -($line->balance);
            $newMoveLine->amount_currency = -($line->amount_currency);

            $newMoveLine->save();
        });
    }

    private function createPaymentTermLine(Move $newMove, Move $record)
    {
        MoveLine::create([
            'move_id'                  => $newMove->id,
            'move_name'                => $newMove->name,
            'display_type'             => 'payment_term',
            'currency_id'              => $newMove->currency_id,
            'partner_id'               => $newMove->partner_id,
            'date_maturity'            => $newMove->invoice_date_due,
            'company_id'               => $newMove->company_id,
            'company_currency_id'      => $newMove->company_currency_id,
            'commercial_partner_id'    => $newMove->partner_id,
            'parent_state'             => $newMove->state,
            'date'                     => now(),
            'creator_id'               => $newMove->creator_id,
            'debit'                    => 0.00,
            'credit'                   => $newMove->amount_total,
            'balance'                  => -$newMove->amount_total,
            'amount_currency'          => -$newMove->amount_total,
            'amount_residual'          => -$newMove->amount_total,
            'amount_residual_currency' => -$newMove->amount_total,
        ]);
    }

    private function createTaxLines(Move $newMove, Move $record)
    {
        $record->taxLines->each(function (MoveLine $line) use ($newMove) {
            $newMoveLine = $line->replicate();

            $newMoveLine->parent_state = $newMove->state;
            $newMoveLine->reference = $newMove->reference;
            $newMoveLine->move_name = null;
            $newMoveLine->move_id = $newMove->id;
            $newMoveLine->debit = $line->credit;
            $newMoveLine->credit = 0.00;
            $newMoveLine->balance = -($line->balance);
            $newMoveLine->amount_currency = -($line->amount_currency);

            $newMoveLine->save();
        });
    }
}
