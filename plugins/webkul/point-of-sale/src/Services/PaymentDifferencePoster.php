<?php

namespace Webkul\PointOfSale\Services;

use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\PointOfSale\Models\Session;

class PaymentDifferencePoster
{
    public function __construct(
        protected AccountResolver $accounts,
    ) {}

    public function post(Session $session, PaymentMethod $paymentMethod, float $difference): ?Move
    {
        if (float_is_zero($difference, precisionDigits: 4)) {
            return null;
        }

        $source = $this->accounts->outstandingAccountFor($paymentMethod);

        $destination = $difference > 0
            ? $this->accounts->cashProfitAccountFor($paymentMethod->journal, $session->company_id)
            : $this->accounts->cashLossAccountFor($paymentMethod->journal, $session->company_id);

        if (! $source || ! $destination || ! $paymentMethod->journal_id) {
            return null;
        }

        $company = $session->config->company;

        $amount = abs($difference);

        $move = Move::create([
            'move_type'   => MoveType::ENTRY,
            'state'       => MoveState::DRAFT,
            'journal_id'  => $paymentMethod->journal_id,
            'company_id'  => $company->id,
            'currency_id' => $session->currency_id ?? $company->currency_id,
            'date'        => $session->stopped_at ?? now(),
            'reference'   => __('point-of-sale::system.session-closer.payment-difference-reference', [
                'method'  => $paymentMethod->name,
                'session' => $session->name,
            ]),
        ]);

        foreach ([
            [$source->id, $difference > 0 ? $amount : 0.0, $difference > 0 ? 0.0 : $amount],
            [$destination->id, $difference > 0 ? 0.0 : $amount, $difference > 0 ? $amount : 0.0],
        ] as [$accountId, $debit, $credit]) {
            MoveLine::create([
                'move_id'     => $move->id,
                'account_id'  => $accountId,
                'name'        => $move->reference,
                'debit'       => $debit,
                'credit'      => $credit,
                'date'        => $move->date,
                'currency_id' => $move->currency_id,
                'company_id'  => $company->id,
            ]);
        }

        AccountFacade::computeAccountMove($move);

        AccountFacade::confirmMove($move->refresh());

        return $move->refresh();
    }
}
