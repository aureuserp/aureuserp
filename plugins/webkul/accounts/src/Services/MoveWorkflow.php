<?php

namespace Webkul\Account\Services;

use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Events\MoveCancelled;
use Webkul\Account\Events\MoveConfirmed;
use Webkul\Account\Events\MoveDrafted;
use Webkul\Account\Events\MoveReversed;
use Webkul\Account\Mail\Invoice\Actions\InvoiceEmail;
use Webkul\Account\Models\FullReconcile;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\PartialReconcile;
use Webkul\Account\Models\Partner;
use Webkul\Support\Services\EmailService;

class MoveWorkflow
{
    public function cancel(AccountMove $move): AccountMove
    {
        $move->state = MoveState::CANCEL;

        $move->save();

        $move = app(MoveCalculator::class)->recompute($move);

        $move->refresh();

        $this->stampLineStates($move, MoveState::CANCEL);

        MoveCancelled::dispatch($move);

        return $move;
    }

    public function post(AccountMove $move): AccountMove
    {
        $this->assertPostable($move);

        $move->state = MoveState::POSTED;

        $move->posted_before = true;

        $move->save();

        $move = app(MoveCalculator::class)->recompute($move);

        $move->refresh();

        $this->stampLineStates($move, MoveState::POSTED);

        $move->refresh();

        if ($move->reversedEntry?->state == MoveState::POSTED) {
            $this->reconcileAgainstReversedEntry($move);
        }

        $this->bumpPartnerRanks($move);

        MoveConfirmed::dispatch($move);

        return $move;
    }

    public function markChecked(AccountMove $move): AccountMove
    {
        $move->checked = true;

        $move->save();

        return app(MoveCalculator::class)->recompute($move);
    }

    public function resetToDraft(AccountMove $move): AccountMove
    {
        if (! in_array($move->state, [MoveState::CANCEL, MoveState::POSTED])) {
            throw new Exception('Only posted/cancelled journal entries can be reset to draft.');
        }

        $this->assertNotExchangeDifference($move);

        $reconciler = app(Reconciler::class);

        $move->lines->each(function (MoveLine $line) use ($reconciler) {
            $line->matchedDebits->each(fn ($partial) => $reconciler->unReconcile($partial));

            $line->matchedCredits->each(fn ($partial) => $reconciler->unReconcile($partial));
        });

        $move->state = MoveState::DRAFT;

        $move->payment_state = PaymentState::NOT_PAID;

        $move->save();

        $move = app(MoveCalculator::class)->recompute($move);

        $move->save();

        $move->refresh();

        $this->stampLineStates($move, MoveState::DRAFT);

        MoveDrafted::dispatch($move);

        return $move;
    }

    public function reverse(Collection $moves, array $defaultValuesPerMove = [], bool $cancel = false): Collection
    {
        if ($cancel) {
            $moves->flatMap(fn (AccountMove $move) => $move->lines)->each(function (MoveLine $line) {
                $line->matchedDebits->each(fn ($partial) => $partial->delete());

                $line->matchedCredits->each(fn ($partial) => $partial->delete());
            });
        }

        $reversals = collect();

        foreach ($moves->values() as $index => $move) {
            $reversals->push($this->buildReversal($move, $defaultValuesPerMove[$index] ?? []));
        }

        foreach ($reversals as $reversal) {
            foreach ($reversal->lines as $line) {
                if ($reversal->move_type === MoveType::ENTRY || $line->display_type === DisplayType::COGS) {
                    $line->update([
                        'balance'         => -$line->balance,
                        'amount_currency' => -$line->amount_currency,
                    ]);
                }
            }
        }

        if ($cancel) {
            $reversals->each(fn (AccountMove $reversal) => $this->post($reversal));
        }

        $moves->each(fn (AccountMove $move) => MoveReversed::dispatch($move));

        return $reversals;
    }

    protected function buildReversal(AccountMove $move, array $defaultValues): AccountMove
    {
        $reversal = $move->replicate();

        $reversal->fill(array_merge($defaultValues, [
            'move_type'         => $move->typeReverseMapping[$move->move_type->value],
            'reversed_entry_id' => $move->id,
            'partner_id'        => $move->partner_id,
        ]));

        $reversal->state = MoveState::DRAFT;

        $reversal->posted_before = false;

        $reversal->name = null;

        $reversal->save();

        foreach ($move->lines as $line) {
            $copy = $line->replicate();

            $copy->move_id = $reversal->id;

            $copy->save();
        }

        return app(MoveCalculator::class)->recompute($reversal);
    }

    public function sendByEmail(AccountMove $move, array $data): AccountMove
    {
        $partners = Partner::whereIn('id', $data['partners'])->get();

        $view = 'accounts::mail/invoice/actions/invoice';

        $attachments = array_map(fn ($file) => ['path' => $file, 'name' => basename($file)], $data['files']);

        $lastPartner = null;

        foreach ($partners as $partner) {
            if (! $partner->email) {
                continue;
            }

            $lastPartner = $partner;

            app(EmailService::class)->send(
                mailClass: InvoiceEmail::class,
                view: $view,
                payload: $this->buildEmailPayload($move, $partner, $data),
                attachments: $attachments,
            );
        }

        $move->addMessage([
            'from' => ['company' => current_company()->toArray()],
            'body' => view($view, ['payload' => $this->buildEmailPayload($move, $lastPartner, $data)])->render(),
            'type' => 'comment',
        ], Auth::user()->id);

        Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/invoice/actions/print-and-send.modal.notification.invoice-sent.title'))
            ->body(__('accounts::filament/resources/invoice/actions/print-and-send.modal.notification.invoice-sent.body'))
            ->send();

        return $move;
    }

    protected function buildEmailPayload(AccountMove $move, $partner, array $data): array
    {
        return [
            'record_name' => $move->name,
            'model_name'  => class_basename($move),
            'subject'     => $data['subject'],
            'description' => $data['description'],
            'to'          => [
                'address' => $partner?->email,
                'name'    => $partner?->name,
            ],
        ];
    }

    public function assertPostable(AccountMove $move): void
    {
        if (! $move->partner_id) {
            if ($move->isSaleDocument(true)) {
                throw new Exception(__('accounts::account-manager.post-action-validate.customer-required'));
            }

            if ($move->isPurchaseDocument(true)) {
                throw new Exception(__('accounts::account-manager.post-action-validate.vendor-required'));
            }
        }

        if ($move->partnerBank?->trashed()) {
            throw new Exception(__('accounts::account-manager.post-action-validate.bank-archived'));
        }

        if (float_compare($move->amount_total, 0, precisionRounding: $move->currency->rounding) < 0) {
            throw new Exception(__('accounts::account-manager.post-action-validate.negative-amount'));
        }

        if (! $move->invoice_date) {
            if ($move->isSaleDocument(true)) {
                $move->invoice_date = now();
            } elseif ($move->isPurchaseDocument(true)) {
                throw new Exception(__('accounts::account-manager.post-action-validate.date-required'));
            }
        }

        if (in_array($move->state, [MoveState::POSTED, MoveState::CANCEL])) {
            throw new Exception(__('accounts::account-manager.post-action-validate.draft-state-required'));
        }

        if ($move->lines->isEmpty()) {
            throw new Exception(__('accounts::account-manager.post-action-validate.lines-required'));
        }

        if ($move->lines->some(fn (MoveLine $line) => $line->account && $line->account->deprecated)) {
            throw new Exception(__('accounts::account-manager.post-action-validate.account-deprecated'));
        }

        if (! $move->journal) {
            throw new Exception(__('accounts::account-manager.post-action-validate.journal-archived'));
        }

        if (! $move->currency) {
            throw new Exception(__('accounts::account-manager.post-action-validate.currency-archived'));
        }
    }

    protected function assertNotExchangeDifference(AccountMove $move): void
    {
        $partialIds = PartialReconcile::where('exchange_move_id', $move->id)->pluck('id')->unique();

        $fullIds = FullReconcile::where('exchange_move_id', $move->id)->pluck('id')->unique();

        if ($partialIds->merge($fullIds)->isNotEmpty()) {
            throw new Exception('You cannot reset to draft an exchange difference journal entry.');
        }
    }

    protected function stampLineStates(AccountMove $move, MoveState $state): void
    {
        foreach ($move->lines as $line) {
            $line->update(['parent_state' => $state]);
        }
    }

    protected function reconcileAgainstReversedEntry(AccountMove $move): void
    {
        $reconciler = app(Reconciler::class);

        $reconciler->reconcileReversals(collect([$move->reversedEntry]), collect([$move]));

        $provisionalNumbers = $move->lines
            ->filter(fn (MoveLine $line) => ! empty($line->matching_number) && str_starts_with($line->matching_number, 'I'))
            ->pluck('matching_number')
            ->unique()
            ->values();

        if ($provisionalNumbers->isEmpty()) {
            return;
        }

        $grouped = MoveLine::with(['move', 'account'])
            ->whereIn('matching_number', $provisionalNumbers)
            ->get()
            ->groupBy(fn (MoveLine $line) => $line->matching_number.':'.$line->account_id);

        foreach ($grouped as $lines) {
            if (! $lines->every(fn (MoveLine $line) => $line->move->state === MoveState::POSTED)) {
                continue;
            }

            $account = $lines->first()->account;

            if (! $account->reconcile) {
                $account->update(['reconcile' => true]);
            }

            $reconciler->reconcile($lines);
        }
    }

    protected function bumpPartnerRanks(AccountMove $move): void
    {
        if ($move->isSaleDocument()) {
            $move->partner?->update(['customer_rank' => DB::raw('COALESCE(customer_rank, 0) + 1')]);

            return;
        }

        if ($move->isPurchaseDocument()) {
            $move->partner?->update(['supplier_rank' => DB::raw('COALESCE(supplier_rank, 0) + 1')]);

            return;
        }

        if ($move->move_type != MoveType::ENTRY) {
            return;
        }

        foreach ([
            [AccountType::ASSET_RECEIVABLE, 'customer_rank'],
            [AccountType::LIABILITY_PAYABLE, 'supplier_rank'],
        ] as [$accountType, $column]) {
            $move->lines
                ->filter(fn (MoveLine $line) => $line->partner_id && $line->account->account_type == $accountType)
                ->pluck('partner')
                ->unique()
                ->each(fn ($partner) => $partner?->update([$column => DB::raw("COALESCE({$column}, 0) + 1")]));
        }
    }
}
