<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Exceptions\UnbalancedSessionException;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Session;

class SessionCloser
{
    public function __construct(
        protected AccountResolver $accounts,
        protected ClosingEntryBuilder $builder,
        protected CogsEntryBuilder $cogs,
        protected SessionPreflight $preflight,
        protected PaymentDifferencePoster $paymentDifferences,
        protected SessionPickingGenerator $sessionPickings,
        protected SessionReconciler $reconciler,
        protected SessionWorkflow $sessions,
    ) {}

    public function close(Session $session, ?float $cashBalanceEndReal = null, ?string $notes = null, ?int $balancingAccountId = null, array $paymentDifferences = []): Session
    {
        return DB::transaction(function () use ($session, $cashBalanceEndReal, $notes, $balancingAccountId, $paymentDifferences): Session {
            $this->preflight->assertCanClose($session);

            $session = $this->sessions->close($session, $cashBalanceEndReal, $notes);

            $this->assertWithinAuthorizedDifference($session, $paymentDifferences);

            $this->sessionPickings->generateFor($session);

            $lines = $this->builder->build($session);

            $delta = $this->builder->balanceDelta($lines);

            if (! float_is_zero($delta, precisionDigits: 4)) {
                $lines = $this->applyBalancingLine($session, $lines, $delta, $balancingAccountId);
            }

            $this->postPaymentDifferences($session, $paymentDifferences);

            $move = $this->postEntry($session, $lines);

            $cogsMove = $this->cogs->post($session);

            $this->markOrdersDone($session);

            $session->forceFill([
                'move_id'      => $move?->id,
                'cogs_move_id' => $cogsMove?->id,
            ])->save();

            if ($move) {
                $this->reconciler->reconcile($session, $move);
            }

            return $session->refresh();
        });
    }

    protected function assertWithinAuthorizedDifference(Session $session, array $paymentDifferences): void
    {
        $config = $session->config;

        if (! $config?->enable_maximum_difference) {
            return;
        }

        $largest = collect($paymentDifferences)
            ->push($session->cash_difference ?? 0)
            ->map(fn ($difference): float => abs((float) $difference))
            ->max();

        if (float_compare($largest, (float) $config->amount_authorized_diff, precisionDigits: 4) <= 0) {
            return;
        }

        if (Auth::user()?->can('update', $config)) {
            return;
        }

        throw new PosConfigurationException(
            __('point-of-sale::system.session-closer.difference-exceeded', [
                'amount' => number_format((float) $config->amount_authorized_diff, 2),
            ])
        );
    }

    protected function applyBalancingLine(Session $session, array $lines, float $delta, ?int $balancingAccountId): array
    {
        $account = $balancingAccountId
            ? $this->accounts->resolve($balancingAccountId, $session->company_id)
            : $this->accounts->balancingAccountFor($session->config);

        if (! $account) {
            throw new UnbalancedSessionException($delta, $session->id);
        }

        $company = $session->config->company;

        $currency = $session->currency ?? $company->currency;

        $lines[] = $this->builder->balancingLine($account, $delta, $currency, $company);

        return $lines;
    }

    protected function postPaymentDifferences(Session $session, array $paymentDifferences): void
    {
        if (empty($paymentDifferences)) {
            return;
        }

        $methods = $session->config->paymentMethods->keyBy('id');

        foreach ($paymentDifferences as $paymentMethodId => $difference) {
            $method = $methods->get((int) $paymentMethodId);

            if (! $method || $method->is_cash_count) {
                continue;
            }

            $this->paymentDifferences->post($session, $method, (float) $difference);
        }
    }

    protected function postEntry(Session $session, array $lines): ?Move
    {
        if (empty($lines)) {
            return null;
        }

        $company = $session->config->company;

        $move = Move::create([
            'move_type'   => MoveType::ENTRY,
            'state'       => MoveState::DRAFT,
            'journal_id'  => $session->config->journal_id,
            'company_id'  => $company->id,
            'currency_id' => $session->currency_id ?? $company->currency_id,
            'date'        => $session->stopped_at ?? now(),
            'reference'   => __('point-of-sale::system.session-closer.entry-reference', ['session' => $session->name]),
        ]);

        foreach ($lines as $line) {
            if (! ($line['account_id'] ?? null)) {
                throw new PosConfigurationException(
                    __('point-of-sale::system.session-closer.line-account-missing')
                );
            }

            MoveLine::create(array_merge($line, [
                'move_id'    => $move->id,
                'date'       => $move->date,
                'company_id' => $company->id,
            ]));
        }

        AccountFacade::computeAccountMove($move);

        AccountFacade::confirmMove($move->refresh());

        return $move->refresh();
    }

    protected function markOrdersDone(Session $session): void
    {
        Order::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->where('state', OrderState::PAID)
            ->update(['state' => OrderState::DONE]);
    }
}
