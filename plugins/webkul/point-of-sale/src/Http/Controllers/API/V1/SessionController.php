<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Webkul\PointOfSale\Enums\CashMovementType;
use Webkul\PointOfSale\Exceptions\SessionHasDraftOrdersException;
use Webkul\PointOfSale\Exceptions\UnbalancedSessionException;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Http\Resources\V1\SessionResource;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Support\PosAccess;

class SessionController extends Controller
{
    protected function authorizeConfig(Config $config): void
    {
        abort_unless(PosAccess::reachesConfig($config), 403);
    }

    protected function authorizeSession(Session $session): void
    {
        abort_unless(PosAccess::reachesSession($session), 403);
    }

    public function current(Config $config): JsonResponse
    {
        $this->authorizeConfig($config);

        $session = PointOfSale::liveSessionFor($config);

        return response()->json([
            'data' => $session ? new SessionResource($session) : null,
        ]);
    }

    public function open(Config $config): JsonResponse
    {
        $this->authorizeConfig($config);

        return response()->json([
            'data' => new SessionResource(PointOfSale::openSession($config)),
        ]);
    }

    public function confirmOpeningControl(Request $request, Session $session): JsonResponse
    {
        $this->authorizeSession($session);

        $data = $request->validate([
            'cash_balance_start' => ['required', 'numeric', 'min:0'],
            'opening_notes'      => ['nullable', 'string'],
        ]);

        return response()->json([
            'data' => new SessionResource(PointOfSale::confirmSessionOpeningControl(
                $session,
                (float) $data['cash_balance_start'],
                $data['opening_notes'] ?? null,
            )),
        ]);
    }

    public function requestClosing(Session $session): JsonResponse
    {
        $this->authorizeSession($session);

        return response()->json([
            'data' => new SessionResource(PointOfSale::requestSessionClosing($session)),
        ]);
    }

    public function close(Request $request, Session $session): JsonResponse
    {
        $this->authorizeSession($session);

        $data = $request->validate([
            'cash_balance_end_real' => ['nullable', 'numeric'],
            'closing_notes'         => ['nullable', 'string'],
            'balancing_account_id'  => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'payment_differences'   => ['nullable', 'array'],
            'payment_differences.*' => ['numeric'],
        ]);

        try {
            $closed = PointOfSale::closeSessionWithAccounting(
                $session,
                isset($data['cash_balance_end_real']) ? (float) $data['cash_balance_end_real'] : null,
                $data['closing_notes'] ?? null,
                isset($data['balancing_account_id']) ? (int) $data['balancing_account_id'] : null,
                collect($data['payment_differences'] ?? [])->mapWithKeys(fn ($difference, $methodId): array => [(int) $methodId => (float) $difference])->all(),
            );
        } catch (SessionHasDraftOrdersException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'code'    => 'draft-orders',
            ], $exception->getStatusCode());
        } catch (UnbalancedSessionException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'code'    => 'unbalanced',
                'delta'   => $exception->delta,
            ], $exception->getStatusCode());
        }

        return response()->json([
            'data' => new SessionResource($closed),
        ]);
    }

    public function cashMovement(Request $request, Session $session): JsonResponse
    {
        $this->authorizeSession($session);

        $data = $request->validate([
            'type'   => ['required', Rule::enum(CashMovementType::class)],
            'amount' => ['required', 'numeric', 'gt:0'],
            'reason' => ['nullable', 'string'],
        ]);

        $type = CashMovementType::from($data['type']);

        $movement = $type === CashMovementType::IN
            ? PointOfSale::cashIn($session, (float) $data['amount'], $data['reason'] ?? null)
            : PointOfSale::cashOut($session, (float) $data['amount'], $data['reason'] ?? null);

        return response()->json([
            'data' => [
                'id'     => $movement->id,
                'type'   => $movement->type,
                'amount' => $movement->amount,
                'reason' => $movement->reason,
            ],
        ]);
    }
}
