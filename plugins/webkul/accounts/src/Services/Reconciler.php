<?php

namespace Webkul\Account\Services;

use Exception;
use Illuminate\Support\Collection;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\FullReconcile;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\PartialReconcile;
use Webkul\Account\Models\Payment;

class Reconciler
{
    public function reconcile(Collection $lines): void
    {
        $lines->load(['account', 'partner', 'currency', 'move', 'company', 'matchedDebits', 'matchedCredits']);

        $this->assertReconcilable($lines);

        $this->reconcileGroups([$lines]);

        Payment::whereIn('move_id', $lines->pluck('move_id')->unique())
            ->get()
            ->each(function (Payment $payment) {
                $payment->computeReconciliationStatus();

                $payment->save();
            });
    }

    public function reconcileReversals($moves, $reverseMoves)
    {
        foreach ($moves->zip($reverseMoves) as [$move, $reverseMove]) {
            $grouped = $move->lines->merge($reverseMove->lines)
                ->reject(fn (MoveLine $line) => $line->reconciled)
                ->groupBy(fn (MoveLine $line) => $line->account_id.'|'.($line->currency_id ?? 'null'));

            foreach ($grouped as $key => $lines) {
                [$accountId] = explode('|', $key);

                $account = Account::find($accountId);

                if ($this->allowsReconciliation($account)) {
                    $this->reconcile($lines);
                }
            }
        }

        return $reverseMoves;
    }

    public function unReconcile($partial): void
    {
        $fullReconcile = $partial->fullReconcile;

        $affectedLineIds = [$partial->debitMove->id, $partial->creditMove->id];

        $debitMove = $partial->debitMove->move;

        $creditMove = $partial->creditMove->move;

        $exchangeMove = $partial->exchangeMove;

        $partial->delete();

        $fullReconcile?->delete();

        if ($exchangeMove) {
            app(MoveWorkflow::class)->reverse(collect([$exchangeMove]), [[
                'date'      => $exchangeMove->date,
                'reference' => __('Reversal of: :name', ['name' => $exchangeMove->name]),
            ]], true);
        }

        $calculator = app(MoveCalculator::class);

        $calculator->recompute($debitMove->refresh())->save();

        $calculator->recompute($creditMove->refresh())->save();

        $this->refreshMatchingNumbers($affectedLineIds);
    }

    public function reconcileGroups(array $plans): array
    {
        $partials = [];

        $fulls = [];

        foreach ($plans as $lines) {
            $results = $this->reconcileNode($this->buildPlan($lines));

            $partials = array_merge($partials, $results['partial_reconciles']);

            $fulls = array_merge($fulls, $results['full_reconciles']);
        }

        return ['partial_reconciles' => $partials, 'full_reconciles' => $fulls];
    }

    protected function buildPlan(Collection $lines): array
    {
        $sorted = $lines->sortBy(fn (MoveLine $line) => [
            $line->date_maturity ?? $line->date,
            $line->currency_id,
            $line->amount_currency,
            $line->balance,
        ]);

        $plan = [
            'lines'    => $sorted,
            'line_ids' => $sorted->pluck('id')->toArray(),
        ];

        $currencies = $sorted->pluck('currency_id')->unique();

        if ($currencies->count() > 1) {
            $plan['nodes'] = $currencies
                ->map(function ($currencyId) use ($sorted) {
                    $subset = $sorted->filter(fn (MoveLine $line) => $line->currency_id == $currencyId);

                    return ['lines' => $subset, 'line_ids' => $subset->pluck('id')->toArray()];
                })
                ->values()
                ->all();
        }

        return $plan;
    }

    protected function reconcileNode(array $plan): array
    {
        $partials = [];

        $fulls = [];

        $settledIds = [];

        foreach ($plan['nodes'] ?? [] as $childPlan) {
            $childResults = $this->reconcileNode($childPlan);

            $partials = array_merge($partials, $childResults['partial_reconciles']);

            $fulls = array_merge($fulls, $childResults['full_reconciles']);

            $settledIds = array_merge($settledIds, $childResults['fully_reconciled_ids']);
        }

        $remaining = ($plan['lines'] ?? collect())->reject(fn (MoveLine $line) => in_array($line->id, $settledIds));

        if ($remaining->isEmpty()) {
            return [
                'partial_reconciles'   => $partials,
                'full_reconciles'      => $fulls,
                'fully_reconciled_ids' => $settledIds,
            ];
        }

        $openAmounts = $remaining->map(fn (MoveLine $line) => [
            'line'                     => $line,
            'amount_residual'          => $line->amount_residual,
            'amount_residual_currency' => $line->amount_residual_currency,
        ])->values()->all();

        $matches = $this->matchDebitsToCredits($openAmounts);

        $touchedLineIds = [];

        foreach ($matches['partials_values_list'] as $match) {
            $partials[] = $this->applyPartial($match, $remaining->first()->company_id, $touchedLineIds);
        }

        if ($touchedLineIds !== []) {
            $this->refreshMatchingNumbers($touchedLineIds);
        }

        $fulls = array_merge($fulls, $this->finalizeFullyMatched($remaining));

        return [
            'partial_reconciles'   => $partials,
            'full_reconciles'      => $fulls,
            'fully_reconciled_ids' => array_merge($settledIds, $matches['fully_reconciled_line_ids']),
        ];
    }

    protected function applyPartial(array $match, $companyId, array &$touchedLineIds): PartialReconcile
    {
        $values = $match['partial_values'];

        $partial = PartialReconcile::create([
            'debit_move_id'          => $values['debit_move_id'],
            'credit_move_id'         => $values['credit_move_id'],
            'amount'                 => $values['amount'],
            'debit_amount_currency'  => $values['debit_amount_currency'],
            'credit_amount_currency' => $values['credit_amount_currency'],
            'company_id'             => $companyId,
        ]);

        $debitLine = MoveLine::find($values['debit_move_id']);

        $creditLine = MoveLine::find($values['credit_move_id']);

        $debitLine->amount_residual -= $values['amount'];
        $debitLine->amount_residual_currency -= $values['debit_amount_currency'];

        if ($debitLine->companyCurrency->isZero($debitLine->amount_residual)) {
            $debitLine->reconciled = true;
        }

        $debitLine->save();

        $creditLine->amount_residual += $values['amount'];
        $creditLine->amount_residual_currency += $values['credit_amount_currency'];

        if ($creditLine->companyCurrency->isZero($creditLine->amount_residual)) {
            $creditLine->reconciled = true;
        }

        $creditLine->save();

        $touchedLineIds[] = $debitLine->id;
        $touchedLineIds[] = $creditLine->id;

        $calculator = app(MoveCalculator::class);

        $calculator->recomputeTotals($debitLine->move->refresh())->save();

        $calculator->recomputeTotals($creditLine->move->refresh())->save();

        if (! empty($match['exchange_values'])) {
            $exchangeMoves = app(ExchangeDifferenceRecorder::class)->postDifferences([$match['exchange_values']]);

            if ($exchangeMoves !== []) {
                $partial->update(['exchange_move_id' => $exchangeMoves[0]->id]);
            }
        }

        return $partial;
    }

    protected function matchDebitsToCredits(array $openAmounts): array
    {
        $debits = array_values(array_filter(
            $openAmounts,
            fn ($values) => $values['line']->balance > 0.0 || $values['line']->amount_currency > 0.0
        ));

        $credits = array_values(array_filter(
            $openAmounts,
            fn ($values) => $values['line']->balance < 0.0 || $values['line']->amount_currency < 0.0
        ));

        $debitIndex = $creditIndex = 0;

        $debitValues = $creditValues = null;

        $settledLineIds = [];

        $matches = [];

        while (true) {
            if (! $debitValues) {
                if ($debitIndex >= count($debits)) {
                    break;
                }

                $debitValues = $debits[$debitIndex++];
            }

            if (! $creditValues) {
                if ($creditIndex >= count($credits)) {
                    break;
                }

                $creditValues = $credits[$creditIndex++];
            }

            $result = $this->matchSinglePair($debitValues, $creditValues);

            if (! empty($result['partial_values'])) {
                $matches[] = $result;
            }

            if ($result['debit_values'] === null) {
                $settledLineIds[] = $debitValues['line']->id;

                $debitValues = null;
            } else {
                $debitValues = $result['debit_values'];
            }

            if ($result['credit_values'] === null) {
                $settledLineIds[] = $creditValues['line']->id;

                $creditValues = null;
            } else {
                $creditValues = $result['credit_values'];
            }
        }

        return [
            'partials_values_list'      => $matches,
            'fully_reconciled_line_ids' => $settledLineIds,
        ];
    }

    protected function matchSinglePair(array $debitValues, array $creditValues): array
    {
        $debitLine = $debitValues['line'];

        $creditLine = $creditValues['line'];

        $debitCurrency = $debitLine->currency;

        $creditCurrency = $creditLine->currency;

        $companyCurrency = $debitLine->company->currency;

        $debitResiduals = $this->residualsPerCurrency($debitValues, $creditCurrency);

        $creditResiduals = $this->residualsPerCurrency($creditValues, $debitCurrency);

        $matchCurrency = match (true) {
            $debitCurrency->id != $companyCurrency->id
                && isset($debitResiduals[$debitCurrency->id], $creditResiduals[$debitCurrency->id]) => $debitCurrency,
            $creditCurrency->id != $companyCurrency->id
                && isset($debitResiduals[$creditCurrency->id], $creditResiduals[$creditCurrency->id]) => $creditCurrency,
            default                                                                                   => $companyCurrency,
        };

        $debitMatch = $debitResiduals[$matchCurrency->id] ?? null;

        $creditMatch = $creditResiduals[$matchCurrency->id] ?? null;

        $result = [
            'debit_values'  => $debitMatch ? $debitValues : null,
            'credit_values' => $creditMatch ? $creditValues : null,
        ];

        if (! $debitMatch || ! $creditMatch) {
            return $result;
        }

        $availableDebit = $debitMatch['residual'];

        $availableCredit = -$creditMatch['residual'];

        $matchable = min($availableDebit, $availableCredit);

        $debitFullyMatched = $availableDebit <= $availableCredit;

        $creditFullyMatched = $availableDebit >= $availableCredit;

        if ($matchCurrency->id == $companyCurrency->id) {
            $amount = $matchable;

            $debitRate = $debitResiduals[$debitCurrency->id]['rate'] ?? null;

            $creditRate = $creditResiduals[$creditCurrency->id]['rate'] ?? null;

            $debitAmountCurrency = $debitRate
                ? min($debitCurrency->round($debitRate * $matchable), $debitValues['amount_residual_currency'])
                : 0.0;

            $creditAmountCurrency = $creditRate
                ? min($creditCurrency->round($creditRate * $matchable), -$creditValues['amount_residual_currency'])
                : 0.0;
        } else {
            $amount = $matchable;

            if ($debitRate = $debitMatch['rate'] ?? null) {
                $amount = min($companyCurrency->round($matchable / $debitRate), $debitValues['amount_residual']);
            }

            if ($creditRate = $creditMatch['rate'] ?? null) {
                $amount = min($companyCurrency->round($matchable / $creditRate), -$creditValues['amount_residual']);
            }

            $debitAmountCurrency = $creditAmountCurrency = $matchable;
        }

        $debitValues['amount_residual'] -= $amount;
        $debitValues['amount_residual_currency'] -= $debitAmountCurrency;
        $creditValues['amount_residual'] += $amount;
        $creditValues['amount_residual_currency'] += $creditAmountCurrency;

        $result['partial_values'] = [
            'amount'                 => $amount,
            'debit_amount_currency'  => $debitAmountCurrency,
            'credit_amount_currency' => $creditAmountCurrency,
            'debit_move_id'          => $debitLine->id,
            'credit_move_id'         => $creditLine->id,
        ];

        $result['debit_values'] = $debitFullyMatched ? null : $debitValues;

        $result['credit_values'] = $creditFullyMatched ? null : $creditValues;

        $exchange = app(ExchangeDifferenceRecorder::class)
            ->differenceFor($debitValues, $creditValues, $debitFullyMatched, $creditFullyMatched);

        if ($exchange) {
            $result['exchange_values'] = $exchange;
        }

        return $result;
    }

    protected function residualsPerCurrency(array $lineValues, $counterpartCurrency): array
    {
        $line = $lineValues['line'];

        $remaining = $lineValues['amount_residual'];

        $remainingCurrency = $lineValues['amount_residual_currency'];

        $companyCurrency = $line->company->currency;

        $residuals = [];

        if (! $companyCurrency->isZero($remaining)) {
            $residuals[$companyCurrency->id] = ['residual' => $remaining, 'rate' => 1.0];
        }

        if ($line->currency->id != $companyCurrency->id && ! $line->currency->isZero($remainingCurrency)) {
            $residuals[$line->currency->id] = [
                'residual' => $remainingCurrency,
                'rate'     => abs($remainingCurrency / $remaining),
            ];
        }

        $needsCounterpart = $counterpartCurrency->id != $companyCurrency->id
            && $line->currency->id == $companyCurrency->id
            && ! $companyCurrency->isZero($remaining);

        if ($needsCounterpart) {
            $rate = $this->conversionRate($counterpartCurrency, $line);

            $inCounterpart = $counterpartCurrency->round($remaining * $rate);

            if (! $counterpartCurrency->isZero($inCounterpart)) {
                $residuals[$counterpartCurrency->id] = ['residual' => $inCounterpart, 'rate' => $rate];
            }
        }

        return $residuals;
    }

    protected function conversionRate($currency, MoveLine $line): float
    {
        $usesForeignCurrency = $line->currency_id != $line->company->currency->id
            && ! $line->company->currency->isZero($line->balance)
            && ! $line->currency->isZero($line->amount_currency);

        return $usesForeignCurrency
            ? abs($line->amount_currency / $line->balance)
            : ($currency->rate ?? 1.0);
    }

    protected function finalizeFullyMatched(Collection $lines): array
    {
        $lines->each->refresh();

        $lines->load(['matchedDebits', 'matchedCredits', 'company.currency', 'currency']);

        $fullReconciles = [];

        $groups = $lines->groupBy(fn (MoveLine $line) => $line->matching_number ?? 'auto_'.$line->id);

        foreach ($groups as $groupLines) {
            if (! $groupLines->every(fn (MoveLine $line) => $this->isSettled($line))) {
                continue;
            }

            $invoicesFullySettled = $groupLines->pluck('move')
                ->filter(fn ($move) => $move->isInvoice(true))
                ->unique()
                ->every(fn ($move) => $move->paymentTermLines->every(fn (MoveLine $line) => $this->isSettled($line)));

            $exchangeMoveId = $this->exchangeMoveForUnsettled($groupLines);

            $partialIds = $groupLines
                ->flatMap(fn (MoveLine $line) => $line->matchedCredits->pluck('id')->merge($line->matchedDebits->pluck('id')))
                ->unique()
                ->all();

            if (! $invoicesFullySettled) {
                $groupLines->each(function (MoveLine $line) {
                    $line->reconciled = true;

                    $line->save();
                });

                continue;
            }

            $fullReconcile = FullReconcile::create(['exchange_move_id' => $exchangeMoveId]);

            PartialReconcile::whereIn('id', $partialIds)->update(['full_reconcile_id' => $fullReconcile->id]);

            $fullReconciles[] = $fullReconcile;

            $this->stampFullReconcile($partialIds, $fullReconcile);
        }

        return $fullReconciles;
    }

    protected function isSettled(MoveLine $line): bool
    {
        return $line->company->currency->isZero($line->amount_residual)
            && $line->currency->isZero($line->amount_residual_currency);
    }

    protected function exchangeMoveForUnsettled(Collection $groupLines): ?int
    {
        $unsettled = $groupLines
            ->reject(fn (MoveLine $line) => $this->isSettled($line))
            ->map(fn (MoveLine $line) => [
                'line'            => $line,
                'amount_residual' => ! $line->company->currency->isZero($line->amount_residual)
                    ? $line->amount_residual
                    : $line->amount_residual_currency,
            ])
            ->all();

        if ($unsettled === []) {
            return null;
        }

        $difference = app(ExchangeDifferenceRecorder::class)->buildExchangeMove($unsettled);

        if (! $difference) {
            return null;
        }

        return app(ExchangeDifferenceRecorder::class)->postDifferences([$difference])[0]->id ?? null;
    }

    protected function stampFullReconcile(array $partialIds, FullReconcile $fullReconcile): void
    {
        $lineIds = PartialReconcile::whereIn('id', $partialIds)
            ->get()
            ->flatMap(fn (PartialReconcile $partial) => [$partial->debit_move_id, $partial->credit_move_id])
            ->unique()
            ->values();

        foreach (MoveLine::whereIn('id', $lineIds)->get() as $line) {
            $line->reconciled = true;
            $line->full_reconcile_id = $fullReconcile->id;
            $line->matching_number = (string) $fullReconcile->id;

            $line->save();
        }
    }

    public function refreshMatchingNumbers(array $lineIds): void
    {
        if ($lineIds === []) {
            return;
        }

        $lines = MoveLine::whereIn('id', $lineIds)->get();

        $partials = $lines
            ->flatMap(fn (MoveLine $line) => $line->matchedDebits->merge($line->matchedCredits))
            ->unique('id')
            ->sortBy('id')
            ->values();

        $groupToLines = [];

        $lineToGroup = [];

        foreach ($partials as $partial) {
            $debitGroup = $lineToGroup[$partial->debit_move_id] ?? null;

            $creditGroup = $lineToGroup[$partial->credit_move_id] ?? null;

            if ($debitGroup && $creditGroup) {
                if ($debitGroup !== $creditGroup) {
                    $this->mergeGroups($groupToLines, $lineToGroup, $debitGroup, $creditGroup);
                }
            } elseif ($debitGroup) {
                $groupToLines[$debitGroup][] = $partial->credit_move_id;
                $lineToGroup[$partial->credit_move_id] = $debitGroup;
            } elseif ($creditGroup) {
                $groupToLines[$creditGroup][] = $partial->debit_move_id;
                $lineToGroup[$partial->debit_move_id] = $creditGroup;
            } else {
                $groupToLines[$partial->id] = [$partial->debit_move_id, $partial->credit_move_id];
                $lineToGroup[$partial->debit_move_id] = $partial->id;
                $lineToGroup[$partial->credit_move_id] = $partial->id;
            }
        }

        foreach ($lines as $line) {
            $line->matching_number = match (true) {
                ! isset($lineToGroup[$line->id]) => null,
                (bool) $line->full_reconcile_id  => (string) $line->full_reconcile_id,
                default                          => 'P'.$lineToGroup[$line->id],
            };

            $line->save();
        }
    }

    protected function mergeGroups(array &$groupToLines, array &$lineToGroup, $debitGroup, $creditGroup): void
    {
        $keep = min($debitGroup, $creditGroup);

        $drop = max($debitGroup, $creditGroup);

        foreach ($groupToLines[$drop] as $lineId) {
            $lineToGroup[$lineId] = $keep;
        }

        $groupToLines[$keep] = array_merge($groupToLines[$keep], $groupToLines[$drop]);

        unset($groupToLines[$drop]);
    }

    public function assertReconcilable(Collection $lines): void
    {
        if ($lines->isEmpty()) {
            return;
        }

        if ($lines->contains(fn (MoveLine $line) => $line->reconciled)) {
            throw new Exception(__('You are trying to reconcile some entries that are already reconciled.'));
        }

        if ($lines->contains(fn (MoveLine $line) => $line->parent_state != MoveState::POSTED)) {
            throw new Exception(__('You can only reconcile posted entries.'));
        }

        $accounts = $lines->pluck('account')->unique();

        if ($accounts->count() > 1) {
            throw new Exception(__(
                'Entries are not from the same account: :accounts',
                ['accounts' => $accounts->pluck('display_name')->implode(', ')]
            ));
        }

        if ($lines->pluck('partner_id')->unique()->count() > 1) {
            throw new Exception('All lines must have the same partner');
        }

        if ($lines->pluck('company')->unique()->count() > 1) {
            throw new Exception(__(
                "Entries don't belong to the same company: :companies",
                ['companies' => $lines->pluck('company')->pluck('display_name')->implode(', ')]
            ));
        }

        if (! $this->allowsReconciliation($accounts->first())) {
            throw new Exception(__(
                'Account :account does not allow reconciliation. First change the configuration of this account to allow it.',
                ['account' => $accounts->first()->display_name]
            ));
        }
    }

    protected function allowsReconciliation($account): bool
    {
        return $account->reconcile
            || in_array($account->account_type, [AccountType::ASSET_CASH, AccountType::LIABILITY_CREDIT_CARD]);
    }
}
