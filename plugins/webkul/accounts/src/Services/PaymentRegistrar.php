<?php

namespace Webkul\Account\Services;

use Exception;
use Illuminate\Support\Arr;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Events\MovePaid;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentRegister;

class PaymentRegistrar
{
    public function register(PaymentRegister $register): void
    {
        $batches = $this->payableBatches($register);

        $singleBatch = $batches[0];

        $editMode = $register->is_single_batch
            && (count($singleBatch['lines']) == 1 || $register->group_payment);

        $pending = $editMode
            ? [$this->singleBatchJob($register, $singleBatch)]
            : $this->perBatchJobs($register, $batches);

        $this->createPayments($register, $pending, $editMode);

        $this->postPayments($pending);

        $this->reconcilePayments($pending);

        $register->lines->map(fn ($line) => $line->move)->unique()
            ->each(fn ($move) => MovePaid::dispatch($move));
    }

    protected function payableBatches(PaymentRegister $register): array
    {
        $batches = collect($register->batches)
            ->filter(function ($batch) use ($register) {
                $account = $register->getBatchAccount($batch);

                return ! $register->require_partner_bank_account || ($account && $account->can_send_money);
            })
            ->values()
            ->all();

        if ($batches === []) {
            throw new Exception(
                'To record payments with '.$register->paymentMethodLine->name.', the recipient bank account must be manually validated. You should go on the partner bank account in order to validate it.'
            );
        }

        return $batches;
    }

    protected function singleBatchJob(PaymentRegister $register, array $batch): array
    {
        $job = [
            'create_vals'  => $this->buildPaymentAttributesForSingleBatch($register, $batch),
            'to_reconcile' => $batch['lines'],
            'batch'        => $batch,
        ];

        if ($register->writeoff_is_exchange_account && $register->currency_id == $register->company_currency_id) {
            $residual = $batch['lines']->sum('amount_residual_currency');

            $job['rate'] = $register->amount ? abs($residual / $register->amount) : 0.0;
        }

        return $job;
    }

    protected function perBatchJobs(PaymentRegister $register, array $batches): array
    {
        if (! $register->group_payment) {
            $linesToPay = in_array($register->installments_mode, ['next', 'overdue', 'before_date'])
                ? $register->getTotalAmountsToPay($batches)['lines']
                : $register->lines;

            $batches = collect($batches)
                ->flatMap(fn ($batch) => collect($batch['lines'])
                    ->filter(fn ($line) => $linesToPay->contains($line))
                    ->map(fn ($line) => array_merge($batch, [
                        'payment_values' => array_merge($batch['payment_values'], [
                            'payment_type' => $line->balance > 0 ? PaymentType::RECEIVE : PaymentType::SEND,
                        ]),
                        'lines' => $line,
                    ])))
                ->all();
        }

        return array_map(fn ($batch) => [
            'create_vals'  => $this->buildPaymentAttributesForBatch($register, $batch),
            'to_reconcile' => $batch['lines'],
            'batch'        => $batch,
        ], $batches);
    }

    public function buildPaymentAttributesForSingleBatch(PaymentRegister $register, array $batch): array
    {
        $attributes = [
            'date'                   => $register->payment_date,
            'amount'                 => $register->amount,
            'payment_type'           => $register->payment_type,
            'partner_type'           => $register->partner_type,
            'memo'                   => $register->communication,
            'journal_id'             => $register->journal_id,
            'company_id'             => $register->company_id,
            'currency_id'            => $register->currency_id,
            'partner_id'             => $register->partner_id,
            'partner_bank_id'        => $register->partner_bank_id,
            'payment_method_line_id' => $register->payment_method_line_id,
            'destination_account_id' => $register->lines[0]->account_id,
            'write_off_line_vals'    => [],
        ];

        $handlesDifference = $register->payment_difference_handling == 'reconcile'
            && ! $register->currency->isZero($register->payment_difference);

        if (! $handlesDifference) {
            return $attributes;
        }

        if ($register->writeoff_is_exchange_account) {
            if ($register->currency_id != $register->company_currency_id) {
                $attributes['force_balance'] = $batch['lines']->sum('amount_residual');
            }

            return $attributes;
        }

        $writeOffAmountCurrency = $register->payment_type == PaymentType::RECEIVE
            ? $register->payment_difference
            : -$register->payment_difference;

        $attributes['write_off_line_vals'][] = [
            'name'            => 'Write Off',
            'account_id'      => $register->writeoff_account_id,
            'partner_id'      => $register->partner_id,
            'currency_id'     => $register->currency_id,
            'amount_currency' => $writeOffAmountCurrency,
            'balance'         => $register->currency->convert(
                $writeOffAmountCurrency,
                $register->company->currency,
                $register->company,
                $register->payment_date
            ),
        ];

        return $attributes;
    }

    public function buildPaymentAttributesForBatch(PaymentRegister $register, array $batch): array
    {
        $values = $register->getValuesFromBatch($batch);

        $partnerBankId = $values['payment_type'] == PaymentType::RECEIVE
            ? $register->journal->bank_account_id
            : data_get($batch, 'payment_values.partner_bank_id');

        $paymentMethodLine = $values['payment_type'] != $register->paymentMethodLine->payment_type
            ? $register->journal->getAvailablePaymentMethodLines($values['payment_type'])->first()
            : $register->paymentMethodLine;

        $attributes = [
            'date'                   => $register->payment_date,
            'amount'                 => $values['source_amount_currency'],
            'payment_type'           => $values['payment_type'],
            'partner_type'           => $values['partner_type'],
            'memo'                   => $register->getCommunication($batch['lines']),
            'journal_id'             => $register->journal_id,
            'company_id'             => $register->company_id,
            'currency_id'            => $values['source_currency_id'],
            'partner_id'             => $values['partner_id'],
            'payment_method_line_id' => $paymentMethodLine,
            'destination_account_id' => $batch['lines'][0]->account_id,
            'write_off_line_vals'    => [],
        ];

        return array_filter($attributes + ['partner_bank_id' => $partnerBankId], fn ($value) => $value !== null);
    }

    public function createPayments(PaymentRegister $register, array &$jobs, bool $editMode = false): void
    {
        $accountingInstalled = (new AccountMove)->getInvoiceInPaymentState() == PaymentState::IN_PAYMENT;

        foreach ($jobs as $index => $job) {
            $attributes = $job['create_vals'];

            $writeOffLines = data_get($attributes, 'write_off_line_vals');

            $forceBalance = data_get($attributes, 'force_balance');

            $lines = data_get($attributes, 'lines');

            $attributes = Arr::except($attributes, ['write_off_line_vals', 'force_balance', 'lines']);

            $payment = Payment::create($attributes);

            if (! $accountingInstalled && ! $payment->outstanding_account_id) {
                $payment->update([
                    'outstanding_account_id' => $payment->getOutstandingAccount($payment->payment_type)->id,
                ]);
            }

            if ($writeOffLines !== null || $forceBalance !== null || $lines !== null) {
                $payment->generateJournalEntry($writeOffLines, $forceBalance, $lines);

                $payment->refresh();

                $moveAttributes = Arr::only($attributes, $payment->moveRelatedFields);

                if (filled($moveAttributes)) {
                    $payment->move->update($moveAttributes);
                }
            }

            $jobs[$index]['payment'] = $payment;

            if ($editMode && $payment->move_id) {
                $this->alignCurrencyRounding($register, $payment, $job['to_reconcile']);
            }
        }
    }

    protected function alignCurrencyRounding(PaymentRegister $register, Payment $payment, $lines): void
    {
        if ($payment->currency_id == $lines->first()->currency_id) {
            return;
        }

        [$liquidityLines, $counterpartLines] = $payment->seekForLines();

        $sourceBalance = abs($lines->sum('amount_residual'));

        $paymentRate = $liquidityLines[0]->balance
            ? $liquidityLines[0]->amount_currency / $liquidityLines[0]->balance
            : 0.0;

        $paymentAmountCurrency = abs($counterpartLines->sum('amount_currency'));

        if (! $payment->currency->isZero(abs($sourceBalance) * $paymentRate - $paymentAmountCurrency)) {
            return;
        }

        $deltaBalance = $sourceBalance - abs($counterpartLines->sum('balance'));

        if ($register->companyCurrency->isZero($deltaBalance)) {
            return;
        }

        $merged = $liquidityLines->merge($counterpartLines);

        $debitLines = $merged->filter(fn ($line) => $line->debit > 0);

        $creditLines = $merged->filter(fn ($line) => $line->credit > 0);

        if ($debitLines->isEmpty() || $creditLines->isEmpty()) {
            return;
        }

        $debitLines[0]->update(['debit' => $debitLines[0]->debit + $deltaBalance]);

        $creditLines[0]->update(['credit' => $creditLines[0]->credit + $deltaBalance]);
    }

    public function postPayments(array $jobs): void
    {
        foreach ($jobs as $job) {
            $this->postPayment($job['payment']);

            app(MoveWorkflow::class)->post($job['payment']->move);
        }
    }

    public function postPayment(Payment $payment): Payment
    {
        if ($payment->require_partner_bank_account && ! $payment->partnerBank->can_send_money) {
            throw new Exception(__(
                'To record payments with :method_name, the recipient bank account must be manually validated. '.
                    'You should go on the partner bank account of :partner in order to validate it.',
                [
                    'method_name' => $payment->paymentMethodLine->name,
                    'partner'     => $payment->partner->display_name,
                ]
            ));
        }

        if ($payment->outstandingAccount->account_type === AccountType::ASSET_CASH) {
            $payment->state = PaymentStatus::PAID;

            $payment->save();
        }

        if (in_array($payment->state, [null, PaymentStatus::DRAFT, PaymentStatus::IN_PROCESS])) {
            $payment->state = PaymentStatus::IN_PROCESS;

            $payment->save();
        }

        return $payment;
    }

    public function reconcilePayments(array $jobs): void
    {
        $reconciler = app(Reconciler::class);

        foreach ($jobs as $job) {
            $payment = $job['payment']->refresh();

            $openLines = $payment->move->lines->filter(
                fn ($line) => $line->parent_state == MoveState::POSTED
                    && in_array($line->account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE])
                    && ! $line->reconciled
            );

            foreach ($openLines->pluck('account_id')->unique() as $accountId) {
                $reconciler->reconcile(
                    $openLines->merge($job['to_reconcile'])->filter(
                        fn ($line) => $line->account_id == $accountId
                            && ! $line->reconciled
                            && $line->parent_state == MoveState::POSTED
                    )
                );
            }

            foreach ($job['to_reconcile'] as $line) {
                $line->move->matchedPayments()->attach($payment->id);
            }

            AccountMove::whereIn('id', $job['to_reconcile']->pluck('move_id')->unique())
                ->get()
                ->each(function (AccountMove $move) {
                    $move->refresh();

                    $move->computePaymentState();

                    $move->save();
                });
        }
    }
}
