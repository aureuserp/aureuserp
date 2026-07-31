<?php

namespace Webkul\Account;

use Illuminate\Support\Collection;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Account\Services\DynamicLineSynchronizer;
use Webkul\Account\Services\ExchangeDifferenceRecorder;
use Webkul\Account\Services\MoveCalculator;
use Webkul\Account\Services\MoveWorkflow;
use Webkul\Account\Services\PaymentRegistrar;
use Webkul\Account\Services\Reconciler;

class AccountManager
{
    public function __construct(
        protected MoveWorkflow $workflow,
        protected MoveCalculator $calculator,
        protected DynamicLineSynchronizer $synchronizer,
        protected Reconciler $reconciler,
        protected PaymentRegistrar $payments,
        protected ExchangeDifferenceRecorder $exchangeDifferences,
    ) {}

    public function cancelMove(AccountMove $record): AccountMove
    {
        return $this->workflow->cancel($record);
    }

    public function confirmMove(AccountMove $record): AccountMove
    {
        return $this->workflow->post($record);
    }

    public function setAsCheckedMove(AccountMove $record): AccountMove
    {
        return $this->workflow->markChecked($record);
    }

    public function resetToDraftMove(AccountMove $record): AccountMove
    {
        return $this->workflow->resetToDraft($record);
    }

    public function printAndSendMove(AccountMove $record, array $data): AccountMove
    {
        return $this->workflow->sendByEmail($record, $data);
    }

    public function reverseMoves($moves, array $defaultValues = [], bool $cancel = false): Collection
    {
        return $this->workflow->reverse(collect($moves), $defaultValues, $cancel);
    }

    public function isConfirmAllowedForMove(AccountMove $record): void
    {
        $this->workflow->assertPostable($record);
    }

    public function computeAccountMove(AccountMove $record): AccountMove
    {
        return $this->calculator->recompute($record);
    }

    public function recomputeMoveTotals(AccountMove $move): AccountMove
    {
        return $this->calculator->recomputeTotals($move);
    }

    public function recomputeMoveLineTotals(MoveLine $line): MoveLine
    {
        return $this->calculator->recomputeLine($line);
    }

    public function productBaseLine(MoveLine $line): array
    {
        return $this->calculator->productBaseLine($line);
    }

    public function roundedBaseAndTaxLines(AccountMove $move, bool $roundFromTaxLines = true): array
    {
        return $this->calculator->roundedBaseAndTaxLines($move, $roundFromTaxLines);
    }

    public function syncGeneratedLines(AccountMove $move): void
    {
        $this->synchronizer->sync($move);
    }

    public function reconcile($lines): void
    {
        $this->reconciler->reconcile($lines);
    }

    public function reconcileReversedMoves($moves, $reverseMoves)
    {
        return $this->reconciler->reconcileReversals($moves, $reverseMoves);
    }

    public function unReconcile($partialReconcile): void
    {
        $this->reconciler->unReconcile($partialReconcile);
    }

    public function isReconciliationAllowedForLines($lines): void
    {
        $this->reconciler->assertReconcilable(collect($lines));
    }

    public function createPayments(PaymentRegister $paymentRegister): void
    {
        $this->payments->register($paymentRegister);
    }

    public function postPayment(Payment $payment): Payment
    {
        return $this->payments->postPayment($payment);
    }

    public function createExchangeDifferenceMoves(array $differences): array
    {
        return $this->exchangeDifferences->postDifferences($differences);
    }
}
