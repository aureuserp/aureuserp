<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Product as AccountProduct;
use Webkul\Account\Models\Tax;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\PriceType;
use Webkul\PointOfSale\Exceptions\OrderAlreadyPaidException;
use Webkul\PointOfSale\Exceptions\OrderNotRefundableException;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Exceptions\RefundExceedsSoldQuantityException;
use Webkul\PointOfSale\Exceptions\SessionNotOpenException;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\OrderLineLot;
use Webkul\PointOfSale\Models\Payment;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Models\Table;
use Webkul\PointOfSale\Support\ClientMessage;
use Webkul\PointOfSale\Support\PosAccess;
use Webkul\Product\Models\Product;

class OrderProcessor
{
    public function __construct(
        protected OrderCalculator $calculator,
        protected OfflinePartnerResolver $partners,
        protected OrderWorkflow $orders,
        protected PosInvoicer $invoicer,
        protected PriceResolver $prices,
        protected SessionWorkflow $sessions,
        protected RefundProcessor $refunds,
    ) {}

    public function process(array $payload): Order
    {
        $uuid = $payload['uuid'] ?? Str::uuid()->toString();

        return DB::transaction(function () use ($payload, $uuid): Order {
            $existing = $this->lockOrder($uuid);

            $this->assertOwnOrder($existing, $payload);

            if ($existing && $existing->state !== OrderState::DRAFT) {
                return $this->settledOrder($existing, $payload);
            }

            $order = $existing
                ? $this->refreshDraft($existing, $payload)
                : $this->createOrder($payload, $uuid);

            if ($order->state !== OrderState::DRAFT) {
                return $this->settledOrder($order, $payload);
            }

            $this->assertLineRules($order, $payload['lines'] ?? []);

            if (array_key_exists('lines', $payload)) {
                $this->syncLines($order, $payload['lines']);

                if ($existing) {
                    $this->pruneLines($order, $payload['lines']);
                }
            }

            $this->syncTip($order);

            $this->assertRefundLines($order);

            $this->syncPayments($order, $payload['payments'] ?? []);

            $order = $this->orders->markPaid($order->refresh());

            return $this->invoiceIfRequested($order);
        });
    }

    protected function assertRefundLines(Order $order): void
    {
        $refundLines = $order->lines()->whereNotNull('refunded_order_line_id')->get();

        foreach ($refundLines as $line) {
            $original = OrderLine::withoutGlobalScopes()->with('order')->find($line->refunded_order_line_id);

            if (! $original?->order || float_compare((float) $line->qty, 0, precisionDigits: 4) >= 0) {
                throw new OrderNotRefundableException(
                    __('point-of-sale::system.order-workflow.refund.nothing-to-refund')
                );
            }

            $this->refunds->assertRefundable($original->order);

            if (float_compare($original->refundableQty(), 0, precisionDigits: 4) < 0) {
                throw new RefundExceedsSoldQuantityException(
                    __('point-of-sale::system.order-workflow.refund.exceeds-sold', [
                        'product' => $original->full_product_name ?? $original->product?->name,
                    ])
                );
            }
        }
    }

    protected function invoiceIfRequested(Order $order): Order
    {
        if (! $order->is_to_invoice || $order->is_invoiced) {
            return $order;
        }

        if ($order->state !== OrderState::PAID) {
            return $order;
        }

        $this->invoicer->invoice($order);

        return $order->refresh();
    }

    public function saveDraft(array $payload): Order
    {
        $uuid = $payload['uuid'] ?? Str::uuid()->toString();

        return DB::transaction(function () use ($payload, $uuid): Order {
            $existing = $this->lockOrder($uuid);

            $this->assertOwnOrder($existing, $payload);

            if ($existing && $existing->state !== OrderState::DRAFT) {
                return $existing;
            }

            $this->assertDraftSessionOpen(
                $existing?->session ?? Session::withoutGlobalScopes()->find($payload['session_id'] ?? null),
                (int) ($payload['config_id'] ?? 0),
            );

            $order = $existing
                ? $this->refreshDraft($existing, $payload)
                : $this->createOrder($payload, $uuid);

            if ($order->state !== OrderState::DRAFT) {
                return $order;
            }

            $this->assertLineRules($order, $payload['lines'] ?? []);

            $this->syncLines($order, $payload['lines'] ?? []);

            $this->pruneLines($order, $payload['lines'] ?? []);

            return $this->calculator->recompute($order->refresh());
        });
    }

    public function saveDraftBatch(array $orders): array
    {
        $data = [];

        $errors = [];

        foreach ($orders as $payload) {
            try {
                $data[] = $this->saveDraft($payload);
            } catch (Throwable $exception) {
                $errors[] = [
                    'uuid'    => $payload['uuid'] ?? null,
                    'message' => ClientMessage::for($exception),
                ];
            }
        }

        return [
            'data'   => $data,
            'errors' => $errors,
        ];
    }

    protected function settledOrder(Order $order, array $payload): Order
    {
        $incoming = collect($payload['payments'] ?? [])->pluck('uuid');

        $recorded = $order->payments()->pluck('uuid');

        if ($incoming->diff($recorded)->isEmpty()) {
            return $order;
        }

        throw new OrderAlreadyPaidException(
            __('point-of-sale::system.order-workflow.mark-paid.already-settled', ['order' => $order->reference])
        );
    }

    protected function assertOwnOrder(?Order $order, array $payload): void
    {
        if (! $order) {
            return;
        }

        $foreign = (int) $order->config_id !== (int) ($payload['config_id'] ?? 0)
            || (Auth::check() && ! PosAccess::reachesOrder($order));

        if ($foreign) {
            throw new PosConfigurationException(__('point-of-sale::system.order-processor.foreign-order'));
        }
    }

    protected function lockOrder(string $uuid): ?Order
    {
        return Order::withoutGlobalScopes()
            ->where('uuid', $uuid)
            ->lockForUpdate()
            ->first();
    }

    protected function assertDraftSessionOpen(?Session $session, int $configId): void
    {
        if ($session && (int) $session->config_id !== $configId) {
            throw new PosConfigurationException(__('point-of-sale::system.order-processor.foreign-order'));
        }

        if ($session?->isLive()) {
            return;
        }

        throw new SessionNotOpenException(
            __('point-of-sale::system.session-workflow.assert-open.not-open', ['session' => $session?->name ?? ''])
        );
    }

    protected function refreshDraft(Order $order, array $payload): Order
    {
        $config = $order->config;

        $order->forceFill([
            'partner_id'         => $this->partners->resolve($payload, $config),
            'note'               => $payload['note'] ?? $order->note,
            'price_list_id'      => $payload['price_list_id'] ?? $order->price_list_id,
            'fiscal_position_id' => $this->resolveFiscalPositionId($config, $payload),
            'table_id'           => $this->resolveTableId($config, $payload['table_id'] ?? null),
            'customer_count'     => (int) ($payload['customer_count'] ?? 0),
            'is_takeaway'        => (bool) ($payload['is_takeaway'] ?? false),
            'is_booked'          => (bool) ($payload['is_booked'] ?? false),
            'floating_name'      => filled($payload['floating_name'] ?? null) ? $payload['floating_name'] : null,
            ...Arr::only($payload, [
                'preparation_state',
                'email',
                'mobile',
                'shipped_at',
            ]),
            'refunded_order_id'  => $payload['refunded_order_id'] ?? $order->refunded_order_id,
            'is_to_invoice'      => (bool) ($payload['is_to_invoice'] ?? $order->is_to_invoice),
        ])->save();

        return $order;
    }

    public function discardDraft(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $order = $this->lockOrder($order->uuid);

            if ($order?->state !== OrderState::DRAFT) {
                return;
            }

            $order->lines()->each(fn (OrderLine $line) => $line->delete());

            $order->delete();
        });
    }

    protected function pruneLines(Order $order, array $lines): void
    {
        $keep = collect($lines)->pluck('uuid')->filter()->all();

        $order->lines()
            ->when($keep, fn ($query) => $query->whereNotIn('uuid', $keep))
            ->get()
            ->each(fn (OrderLine $line) => $line->delete());
    }

    public function processBatch(array $orders): array
    {
        $data = [];

        $errors = [];

        foreach ($orders as $payload) {
            try {
                $data[] = $this->process($payload);
            } catch (Throwable $exception) {
                $errors[] = [
                    'uuid'    => $payload['uuid'] ?? null,
                    'message' => ClientMessage::for($exception),
                    'code'    => $exception instanceof OrderAlreadyPaidException ? 'already-settled' : null,
                ];
            }
        }

        return [
            'data'   => $data,
            'errors' => $errors,
        ];
    }

    public function resolveSession(Config $config, ?int $sessionId = null): Session
    {
        $session = $sessionId
            ? Session::withoutGlobalScopes()->whereKey($sessionId)->first()
            : null;

        if ($session && $session->config_id === $config->id && $session->isLive()) {
            return $session;
        }

        $live = $this->sessions->liveSessionFor($config);

        if ($live) {
            return $live;
        }

        return $session && $session->config_id === $config->id
            ? $this->sessions->openRescueFor($session)
            : $this->sessions->open($config);
    }

    protected function createOrder(array $payload, string $uuid): Order
    {
        $config = Config::withoutGlobalScopes()->findOrFail($payload['config_id']);

        $session = $this->resolveSession($config, $payload['session_id'] ?? null);

        $payload['partner_id'] = $this->partners->resolve($payload, $config);

        $payload['table_id'] = $this->resolveTableId($config, $payload['table_id'] ?? null);

        $payload['fiscal_position_id'] = $this->resolveFiscalPositionId($config, $payload);

        $attributes = array_merge(
            Arr::only($payload, [
                'reference',
                'tracking_number',
                'sequence_number',
                'note',
                'email',
                'mobile',
                'partner_id',
                'price_list_id',
                'fiscal_position_id',
                'shipped_at',
                'is_to_invoice',
                'customer_count',
                'is_takeaway',
                'is_booked',
                'preparation_state',
                'floating_name',
                'table_id',
                'refunded_order_id',
            ]),
            [
                'uuid'       => $uuid,
                'config_id'  => $config->id,
                'session_id' => $session->id,
                'ordered_at' => filled($payload['ordered_at'] ?? null)
                    ? Date::parse($payload['ordered_at'])->setTimezone(config('app.timezone'))
                    : now(),
            ],
        );

        try {
            return DB::transaction(fn (): Order => Order::create($attributes));
        } catch (UniqueConstraintViolationException) {
            return Order::withoutGlobalScopes()
                ->where('uuid', $uuid)
                ->lockForUpdate()
                ->firstOrFail();
        }
    }

    protected function syncTip(Order $order): void
    {
        $config = $order->config;

        if (! $config?->enable_tip || ! $config->tip_product_id) {
            return;
        }

        $amount = (float) $order->lines()
            ->where('product_id', $config->tip_product_id)
            ->get()
            ->sum(fn (OrderLine $line): float => (float) $line->qty * (float) $line->price_unit);

        if (float_is_zero($amount, precisionDigits: 4)) {
            return;
        }

        $order->forceFill([
            'is_tipped'  => true,
            'tip_amount' => $amount,
        ])->save();
    }

    protected function resolveFiscalPositionId(Config $config, array $payload): ?int
    {
        if (! empty($payload['is_takeaway']) && $config->is_restaurant && $config->enable_takeaway && $config->takeaway_fiscal_position_id) {
            return (int) $config->takeaway_fiscal_position_id;
        }

        $allowed = $config->allowedFiscalPositionIds();

        $requested = (int) ($payload['fiscal_position_id'] ?? 0);

        if ($requested && in_array($requested, $allowed, true)) {
            return $requested;
        }

        return $config->usesFiscalPositions() && $config->fiscal_position_id
            ? (int) $config->fiscal_position_id
            : null;
    }

    protected function assertLineRules(Order $order, array $lines): void
    {
        $config = $order->config;

        if (! $config) {
            return;
        }

        $exempt = array_filter([
            $config->enable_tip ? (int) $config->tip_product_id : 0,
            $config->enable_global_discount ? (int) $config->discount_product_id : 0,
        ]);

        $priceLocked = $config->enable_price_control && ! Auth::user()?->can('update', $config);

        $storedPrices = $priceLocked ? $order->lines()->pluck('price_unit', 'uuid') : collect();

        foreach ($lines as $line) {
            $productId = (int) ($line['product_id'] ?? 0);

            if (in_array($productId, $exempt, true) || ! empty($line['refunded_order_line_id'])) {
                continue;
            }

            if (! $config->enable_line_discount && float_compare((float) ($line['discount'] ?? 0), 0, precisionDigits: 4) > 0) {
                throw new PosConfigurationException(__('point-of-sale::system.order-processor.line-discount-disabled'));
            }

            if (! $priceLocked || ! array_key_exists('price_unit', $line)) {
                continue;
            }

            $stored = $storedPrices->get($line['uuid'] ?? '');

            if ($stored !== null && float_compare((float) $line['price_unit'], (float) $stored, precisionDigits: 4) === 0) {
                continue;
            }

            $product = Product::withoutGlobalScopes()->find($productId);

            if (! $product) {
                continue;
            }

            $resolved = $this->prices->resolveForOrder($order, $product, (float) ($line['qty'] ?? 1));

            if (float_compare((float) $line['price_unit'], $resolved, precisionDigits: 4) !== 0) {
                throw new PosConfigurationException(__('point-of-sale::system.order-processor.price-locked', ['product' => $product->name]));
            }
        }
    }

    protected function resolveTableId(Config $config, mixed $tableId): ?int
    {
        if (blank($tableId) || ! $config->is_restaurant) {
            return null;
        }

        return Table::query()
            ->whereKey($tableId)
            ->whereHas('floor.configs', fn ($query) => $query->whereKey($config->id))
            ->value('id');
    }

    protected function syncLines(Order $order, array $lines): void
    {
        $uuids = collect($lines)->pluck('uuid')->filter()->all();

        $foreign = $uuids && OrderLine::withoutGlobalScopes()
            ->whereIn('uuid', $uuids)
            ->where('order_id', '!=', $order->id)
            ->exists();

        if ($foreign) {
            throw new PosConfigurationException(__('point-of-sale::system.order-processor.foreign-line'));
        }

        foreach ($lines as $line) {
            $model = OrderLine::updateOrCreate(
                ['uuid' => $line['uuid'] ?? Str::uuid()->toString()],
                array_merge(
                    [
                        'price_unit' => $this->resolvePrice($order, $line),
                        'price_type' => array_key_exists('price_unit', $line) ? PriceType::MANUAL : PriceType::ORIGINAL,
                    ],
                    Arr::only($line, [
                        'product_id',
                        'uom_id',
                        'qty',
                        'price_unit',
                        'price_extra',
                        'price_type',
                        'discount',
                        'customer_note',
                        'note',
                        'refunded_order_line_id',
                        'route_id',
                        'warehouse_id',
                    ]),
                    ['order_id' => $order->id],
                ),
            );

            if (array_key_exists('tax_ids', $line)) {
                $model->taxes()->sync($line['tax_ids']);
            } else {
                $product = Product::withoutGlobalScopes()->find($model->product_id);

                $model->taxes()->sync($this->defaultTaxIds($product, $order->company_id));
            }

            if (array_key_exists('attribute_value_ids', $line)) {
                $model->attributeValues()->sync($line['attribute_value_ids']);
            }

            if (array_key_exists('lots', $line)) {
                $this->syncLots($model, $line['lots']);
            }
        }
    }

    protected function syncLots(OrderLine $line, array $lots): void
    {
        $line->lots()->delete();

        foreach ($lots as $lot) {
            if (blank($lot['lot_name'] ?? null)) {
                continue;
            }

            OrderLineLot::create([
                'order_line_id' => $line->id,
                'lot_name'      => $lot['lot_name'],
                'lot_id'        => $lot['lot_id'] ?? null,
                'qty'           => (float) ($lot['qty'] ?? 1),
            ]);
        }
    }

    protected function resolvePrice(Order $order, array $line): float
    {
        if (array_key_exists('price_unit', $line)) {
            return (float) $line['price_unit'];
        }

        $product = Product::withoutGlobalScopes()->find($line['product_id'] ?? null);

        if (! $product) {
            return 0.0;
        }

        return $this->prices->resolveForOrder($order, $product, (float) ($line['qty'] ?? 1));
    }

    protected function defaultTaxIds(?Product $product, ?int $companyId): array
    {
        if (! $product) {
            return [];
        }

        $accountProduct = AccountProduct::withoutGlobalScopes()->find($product->id);

        if (! $accountProduct) {
            return [];
        }

        return Tax::forProduct($accountProduct, TypeTaxUse::SALE, $companyId);
    }

    protected function syncPayments(Order $order, array $payments): void
    {
        foreach ($payments as $payment) {
            Payment::updateOrCreate(
                ['uuid' => $payment['uuid'] ?? Str::uuid()->toString()],
                array_merge(
                    Arr::only($payment, [
                        'payment_method_id',
                        'amount',
                        'terminal_status',
                        'transaction_reference',
                        'card_type',
                        'card_brand',
                        'cardholder_name',
                        'ticket',
                        'paid_at',
                    ]),
                    [
                        'order_id'   => $order->id,
                        'session_id' => $order->session_id,
                    ],
                ),
            );
        }
    }
}
