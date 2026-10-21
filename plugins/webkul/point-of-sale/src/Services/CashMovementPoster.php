<?php

namespace Webkul\PointOfSale\Services;

use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\PointOfSale\Enums\CashMovementType;
use Webkul\PointOfSale\Models\CashMovement;

class CashMovementPoster
{
    public function __construct(
        protected AccountResolver $accounts,
    ) {}

    public function post(CashMovement $cashMovement): ?Move
    {
        $session = $cashMovement->session;

        $config = $session?->config;

        $journal = $session?->resolveCashJournal();

        if (! $config || ! $journal) {
            return null;
        }

        $liquidity = $this->accounts->resolve($journal->default_account_id, $session->company_id);

        $counterpart = $this->accounts->cashMovementAccountFor($config);

        if (! $liquidity || ! $counterpart) {
            return null;
        }

        $company = $config->company;

        $amount = abs((float) $cashMovement->amount);

        $isIn = $cashMovement->type === CashMovementType::IN;

        $move = Move::create([
            'move_type'   => MoveType::ENTRY,
            'state'       => MoveState::DRAFT,
            'journal_id'  => $journal->id,
            'company_id'  => $company->id,
            'currency_id' => $session->currency_id ?? $company->currency_id,
            'date'        => $cashMovement->created_at ?? now(),
            'reference'   => $cashMovement->reason ?: $session->name,
        ]);

        foreach ([
            [$liquidity->id, $isIn ? $amount : 0.0, $isIn ? 0.0 : $amount],
            [$counterpart->id, $isIn ? 0.0 : $amount, $isIn ? $amount : 0.0],
        ] as [$accountId, $debit, $credit]) {
            MoveLine::create([
                'move_id'     => $move->id,
                'account_id'  => $accountId,
                'name'        => $cashMovement->reason ?: $session->name,
                'debit'       => $debit,
                'credit'      => $credit,
                'date'        => $move->date,
                'currency_id' => $move->currency_id,
                'company_id'  => $company->id,
            ]);
        }

        AccountFacade::computeAccountMove($move);

        AccountFacade::confirmMove($move->refresh());

        $cashMovement->forceFill(['move_id' => $move->id])->save();

        return $move->refresh();
    }
}
