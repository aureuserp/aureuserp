<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Str;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveReversal;
use Webkul\Support\Traits\PDFHandler;

class ReverseAction extends Action
{
    use PDFHandler;

    protected static string $resource = CreditNoteResource::class;

    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.credit-note';
    }

    public function setResource(string $resource): self
    {
        static::$resource = $resource;

        return $this;
    }

    /**
     * @return class-string
     */
    public static function getResource(): string
    {
        return static::$resource;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/reverse.title'))
            ->color('gray')
            ->visible(fn (Move $record) => $record->state == MoveState::POSTED)
            ->icon('heroicon-o-receipt-refund')
            ->modalHeading(__('accounts::filament/resources/invoice/actions/reverse.modal.heading'));

        $this->schema(
            function (Schema $schema) {
                return $schema->components([
                    Textarea::make('reason')
                        ->label(__('accounts::filament/resources/invoice/actions/reverse.modal.form.reason'))
                        ->maxLength(245)
                        ->required(),
                    Select::make('journal_id')
                        ->label(__('accounts::filament/resources/invoice/actions/reverse.modal.form.journal'))
                        ->relationship(
                            'journal',
                            'name',
                            modifyQueryUsing: fn ($query) => $query->whereIn('id', [$this->getRecord()->journal_id])
                        )
                        ->required()
                        ->default(fn () => $this->getRecord()->journal_id),
                    DatePicker::make('date')
                        ->label(__('accounts::filament/resources/invoice/actions/reverse.modal.form.date'))
                        ->default(now())
                        ->native(false)
                        ->required(),
                ]);
            }
        );

        $this->action(function (Move $record, array $data, $livewire) {
            $moveReversal = MoveReversal::create([
                'journal_id' => $data['journal_id'],
                'date'       => $data['date'],
                'reason'     => $data['reason'],
            ]);

            $moveReversal->moves()->attach($record);

            $defaultValues = $this->prepareMoveValues($record, $moveReversal);

            $isCancelNeeded = ! $defaultValues['auto_post'] && $record->move_type == MoveType::ENTRY;

            $reversedMoves = AccountFacade::reverseMoves(
                collect([$record]),
                $defaultValues,
                $isCancelNeeded
            )->each(function (Move $move) use ($moveReversal) {
                $moveReversal->newMoves()->attach($move->id);
            });

            AccountFacade::computeAccountMove($record);

            $redirectUrl = static::getResource()::getUrl('edit', ['record' => $reversedMoves->first()->id]);

            $livewire->redirect($redirectUrl, navigate: FilamentView::hasSpaMode());
        });
    }

    private function prepareMoveValues(Move $move, MoveReversal $moveReversal): array
    {
        return [
            'reference'         => Str::limit(
                "Reversal of: {$move->name}, {$moveReversal->reason}",
                250
            ),
            'date'              => $moveReversal->date,
            'invoice_date_due'  => $moveReversal->date,
            'invoice_date'      => $move->isInvoice(true) ? $moveReversal->date : null,
            'journal_id'        => $moveReversal->journal_id,
            'invoice_user_id'   => $move->invoice_user_id,
            'auto_post'         => 0,
        ];
    }
}
