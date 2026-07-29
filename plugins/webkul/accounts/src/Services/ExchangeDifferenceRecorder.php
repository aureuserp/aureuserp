<?php

namespace Webkul\Account\Services;

use Exception;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Settings\DefaultAccountSettings;

class ExchangeDifferenceRecorder
{
    public function differenceFor($debitValues, $creditValues, bool $debitFullyMatched, bool $creditFullyMatched): ?array
    {
        $unmatchedRemainders = [];

        foreach ([[$debitFullyMatched, $debitValues], [$creditFullyMatched, $creditValues]] as [$fullyMatched, $values]) {
            if (! $fullyMatched || ! $values) {
                continue;
            }

            if ($values['line']->company->currency->isZero($values['amount_residual'])) {
                continue;
            }

            $unmatchedRemainders[] = [
                'line'            => $values['line'],
                'amount_residual' => $values['amount_residual'],
            ];
        }

        return $unmatchedRemainders === []
            ? null
            : $this->buildExchangeMove($unmatchedRemainders);
    }

    public function buildExchangeMove(array $remainders): ?array
    {
        if ($remainders === []) {
            return null;
        }

        $settings = new DefaultAccountSettings;

        $journalId = $settings->currency_exchange_journal_id;

        $expenseAccountId = $settings->expense_currency_exchange_account_id;

        $incomeAccountId = $settings->income_currency_exchange_account_id;

        if (! $journalId || ! $expenseAccountId || ! $incomeAccountId) {
            throw new Exception('Exchange difference journal and accounts must be configured');
        }

        $moveValues = [
            'move_type'  => MoveType::ENTRY,
            'date'       => now()->format('Y-m-d'),
            'journal_id' => $journalId,
            'lines'      => [],
        ];

        $toReconcile = [];

        $sequence = 0;

        foreach ($remainders as $remainder) {
            $line = $remainder['line'];

            $residual = $remainder['amount_residual'];

            if ($line->company->currency->isZero($residual)) {
                continue;
            }

            $moveValues['lines'][] = [
                'name'            => 'Currency exchange rate difference',
                'debit'           => $residual < 0 ? abs($residual) : 0,
                'credit'          => $residual > 0 ? $residual : 0,
                'amount_currency' => 0,
                'account_id'      => $line->account_id,
                'currency_id'     => $line->currency_id,
                'partner_id'      => $line->partner_id,
            ];

            $moveValues['lines'][] = [
                'name'            => 'Currency exchange rate difference',
                'debit'           => $residual > 0 ? $residual : 0,
                'credit'          => $residual < 0 ? abs($residual) : 0,
                'amount_currency' => 0,
                'account_id'      => $residual < 0 ? $incomeAccountId : $expenseAccountId,
                'currency_id'     => $line->currency_id,
                'partner_id'      => $line->partner_id,
            ];

            $toReconcile[] = ['source_line' => $line, 'sequence' => $sequence];

            $sequence += 2;
        }

        return ['move_values' => $moveValues, 'to_reconcile' => $toReconcile];
    }

    public function postDifferences(array $differences): array
    {
        $moves = [];

        foreach ($differences as $difference) {
            $moveValues = $difference['move_values'];

            if ($moveValues['lines'] === []) {
                continue;
            }

            $move = AccountMove::create($moveValues);

            $move->state = 'posted';

            $move->save();

            foreach ($moveValues['lines'] as $lineValues) {
                MoveLine::create($lineValues + ['move_id' => $move->id]);
            }

            $move = app(MoveCalculator::class)->recompute($move);

            foreach ($difference['to_reconcile'] as $pairing) {
                $exchangeLine = $move->lines[$pairing['sequence']];

                app(Reconciler::class)->reconcileGroups([
                    MoveLine::whereIn('id', [$pairing['source_line']->id, $exchangeLine->id])->get(),
                ]);
            }

            $moves[] = $move;
        }

        return $moves;
    }
}
