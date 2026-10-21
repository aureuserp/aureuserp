<?php

namespace Webkul\PointOfSale\Livewire;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Exceptions\SessionHasDraftOrdersException;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Filament\Pos\Pages\Orders;
use Webkul\PointOfSale\Filament\Pos\Pages\Pos;
use Webkul\PointOfSale\Models\Bill;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Services\ClosingControlReport;
use Webkul\PointOfSale\Services\SessionSalesDetailsReport;

class RegisterControls extends Component
{
    public Session $session;

    public Config $config;

    public float $openingCash = 0;

    public ?string $openingNote = null;

    public ?float $previousClosingBalance = null;

    public array $moneyDetails = [];

    public string $moneyDetailsTarget = 'opening';

    public string $cashMovementType = 'in';

    public ?float $cashMovementAmount = null;

    public ?string $cashMovementReason = null;

    public ?float $closingCash = null;

    public ?string $closingNote = null;

    public array $paymentCounted = [];

    public bool $showCashMoves = false;

    public function mount(Session $session): void
    {
        $this->session = $session;

        $this->config = $session->config;

        if ($this->needsOpeningControl()) {
            $this->previousClosingBalance = $this->config->lastClosedSession?->cash_balance_end_real;

            $this->openingCash = $this->roundAmount((float) ($this->previousClosingBalance ?? 0));
        }
    }

    public function updatedOpeningCash($value): void
    {
        $this->openingCash = $this->roundAmount((float) $value);
    }

    public function roundAmount(float $amount): float
    {
        return round(float_round($amount, precisionDigits: 2), 2);
    }

    public function needsOpeningControl(): bool
    {
        return $this->session->state === SessionState::OPENING_CONTROL;
    }

    public function hasCashPaymentMethod(): bool
    {
        return $this->config->cashPaymentMethod() !== null;
    }

    public function money(float $amount): string
    {
        return money($amount, $this->config->currency?->name ?? $this->config->company?->currency?->name);
    }

    public function downloadSalesDetails(): StreamedResponse
    {
        $report = app(SessionSalesDetailsReport::class)->build($this->session->refresh());

        $pdf = Pdf::loadView('point-of-sale::filament.pos.reports.sales-details', [
            ...$report,
            'money' => fn (float $amount): string => $this->money($amount),
        ]);

        $pdf->setPaper('a4', 'portrait');

        $name = __('point-of-sale::filament/pos/reports/sales-details.filename')
            .' - '.str_replace('/', '_', (string) $this->session->name).'.pdf';

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->output();
        }, $name);
    }

    public function getBills(): Collection
    {
        return Bill::query()
            ->where(fn (Builder $query) => $query
                ->where('is_for_all_configs', true)
                ->orWhereHas('configs', fn (Builder $configs) => $configs->whereKey($this->config->id)))
            ->orderByDesc('value')
            ->get();
    }

    public function openMoneyDetails(string $target = 'opening'): void
    {
        $this->moneyDetailsTarget = $target;

        $this->dispatch('open-modal', id: 'pos-money-details');
    }

    public function stepMoneyDetail(int|string $billId, int $step): void
    {
        $quantity = (int) ($this->moneyDetails[(string) $billId] ?? 0) + $step;

        $this->moneyDetails[(string) $billId] = max(0, $quantity);
    }

    public function moneyDetailsTotal(): float
    {
        $bills = $this->getBills()->keyBy('id');

        return $this->roundAmount(collect($this->moneyDetails)->reduce(
            fn (float $total, $quantity, $billId): float => $total
                + ((float) ($bills->get((int) $billId)?->value ?? 0) * (int) $quantity),
            0.0,
        ));
    }

    public function confirmMoneyDetails(): void
    {
        $total = $this->moneyDetailsTotal();

        if ($this->moneyDetailsTarget === 'closing') {
            $this->closingCash = $total;

            $this->closingNote = $this->moneyDetailsNote($total);
        } else {
            $this->openingCash = $total;

            $this->openingNote = $this->moneyDetailsNote($total);
        }

        $this->dispatch('close-modal', id: 'pos-money-details');
    }

    protected function moneyDetailsNote(float $total): ?string
    {
        if (float_is_zero($total, precisionDigits: 2)) {
            return null;
        }

        $heading = $this->moneyDetailsTarget === 'closing' ? 'closing-heading' : 'opening-heading';

        $note = __("point-of-sale::filament/pos/pages/terminal.money-details.{$heading}")."\n";

        foreach ($this->getBills()->sortBy('value') as $bill) {
            $quantity = (int) ($this->moneyDetails[(string) $bill->id] ?? 0);

            if ($quantity === 0) {
                continue;
            }

            $note .= "\t{$quantity} x ".$this->money((float) $bill->value)."\n";
        }

        return $note.__('point-of-sale::filament/pos/pages/terminal.money-details.total', [
            'total' => $this->money($total),
        ]);
    }

    public function confirmOpening(): void
    {
        try {
            $this->session = PointOfSale::confirmSessionOpeningControl(
                $this->session,
                $this->roundAmount((float) $this->openingCash),
                $this->openingNote,
            );
        } catch (Throwable $exception) {
            Notification::make()
                ->danger()
                ->body($exception->getMessage())
                ->send();

            return;
        }

        $this->dispatch('close-modal', id: 'pos-opening-control');
    }

    #[On('pos-open-cash-movement')]
    public function openCashMovementFromMenu(): void
    {
        $this->openCashMovement('in');
    }

    public function openCashMovement(string $type): void
    {
        $this->cashMovementType = $type;

        $this->cashMovementAmount = null;

        $this->cashMovementReason = null;

        $this->dispatch('open-modal', id: 'pos-cash-movement');
    }

    public function recordCashMovement(): void
    {
        try {
            $amount = $this->roundAmount((float) $this->cashMovementAmount);

            $this->cashMovementType === 'in'
                ? PointOfSale::cashIn($this->session, $amount, $this->cashMovementReason)
                : PointOfSale::cashOut($this->session, $amount, $this->cashMovementReason);

            if ($this->closingCash !== null) {
                $this->closingCash = $this->roundAmount(
                    (float) $this->closingCash + ($this->cashMovementType === 'in' ? $amount : -$amount)
                );
            }

            $this->dispatch('close-modal', id: 'pos-cash-movement');

            Notification::make()
                ->success()
                ->title(__('point-of-sale::filament/pos/pages/terminal.cash-movement.notification.title'))
                ->body(__('point-of-sale::filament/pos/pages/terminal.cash-movement.notification.body.'.$this->cashMovementType, [
                    'amount' => $this->money($amount),
                ]))
                ->send();
        } catch (Throwable $exception) {
            Notification::make()
                ->danger()
                ->body($exception->getMessage())
                ->send();
        }
    }

    #[On('pos-close-register')]
    public function closeRegisterFromMenu(): void
    {
        $this->goToClosing();
    }

    public function goToClosing(): void
    {
        $this->moneyDetails = [];

        $this->closingCash = null;

        $this->closingNote = null;

        $this->paymentCounted = [];

        $this->showCashMoves = false;

        $this->dispatch('open-modal', id: 'pos-closing');
    }

    public function toggleCashMoves(): void
    {
        $this->showCashMoves = ! $this->showCashMoves;
    }

    public function closingControl(): array
    {
        return app(ClosingControlReport::class)->build($this->session->refresh());
    }

    public function paymentDifference(array $method): float
    {
        $counted = $this->paymentCounted[$method['id']] ?? null;

        return $counted === null || $counted === ''
            ? 0.0
            : $this->roundAmount((float) $counted - $method['amount']);
    }

    public function copyExpectedCash(): void
    {
        $this->closingCash = $this->roundAmount((float) $this->session->expectedCashBalance());
    }

    public function copyExpectedPayment(int $methodId, float $amount): void
    {
        $this->paymentCounted[$methodId] = $this->roundAmount($amount);
    }

    public function exceedsAuthorizedDifference(array $control): bool
    {
        if ($control['amount_authorized_diff'] === null || Auth::user()?->can('update', $this->config)) {
            return false;
        }

        $cash = $control['default_cash_details'] ?? null;

        $largest = collect($this->paymentDifferences())
            ->push($cash ? $this->cashDifference($cash) : 0)
            ->map(fn ($difference): float => abs((float) $difference))
            ->max();

        return float_compare($largest, (float) $control['amount_authorized_diff'], precisionDigits: 2) > 0;
    }

    public function cashDifference(array $cash): float
    {
        return $this->closingCash === null
            ? 0.0
            : $this->roundAmount((float) $this->closingCash - $cash['amount']);
    }

    protected function paymentDifferences(): array
    {
        $methods = collect($this->closingControl()['non_cash_payment_methods']);

        return collect($this->paymentCounted)
            ->filter(fn ($counted): bool => $counted !== null && $counted !== '')
            ->mapWithKeys(function ($counted, $methodId) use ($methods): array {
                $amount = (float) ($methods->firstWhere('id', (int) $methodId)['amount'] ?? 0);

                return [(int) $methodId => $this->roundAmount((float) $counted - $amount)];
            })
            ->all();
    }

    public function closeRegister(): void
    {
        try {
            PointOfSale::closeSessionWithAccounting(
                $this->session,
                $this->closingCash === null ? null : $this->roundAmount((float) $this->closingCash),
                $this->closingNote,
                null,
                $this->paymentDifferences(),
            );

            $this->redirect(Pos::getUrl(), navigate: FilamentView::hasSpaMode());
        } catch (SessionHasDraftOrdersException $exception) {
            if (! $this->config->is_restaurant) {
                Notification::make()
                    ->danger()
                    ->body($exception->getMessage())
                    ->send();

                return;
            }

            $this->dispatch('close-modal', id: 'pos-closing');

            $this->dispatch('open-modal', id: 'pos-draft-orders');
        } catch (Throwable $exception) {
            Notification::make()
                ->danger()
                ->body($exception->getMessage())
                ->send();
        }
    }

    public function ordersUrl(): string
    {
        return Orders::getUrl([
            'session' => $this->session->getKey(),
            'status'  => OrderState::DRAFT->value,
        ]);
    }

    public function cancelDraftOrders(): void
    {
        try {
            $this->draftOrdersQuery()->get()->each(fn (Order $order) => PointOfSale::cancelOrder($order));
        } catch (Throwable $exception) {
            Notification::make()
                ->danger()
                ->body($exception->getMessage())
                ->send();

            return;
        }

        $this->dispatch('close-modal', id: 'pos-draft-orders');

        $this->closeRegister();
    }

    protected function draftOrdersQuery(): Builder
    {
        return Order::withoutGlobalScopes()
            ->where('session_id', $this->session->id)
            ->where('state', OrderState::DRAFT);
    }

    public function render(): View
    {
        return view('point-of-sale::livewire.register-controls');
    }
}
