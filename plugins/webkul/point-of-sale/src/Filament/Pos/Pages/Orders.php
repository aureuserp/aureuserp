<?php

namespace Webkul\PointOfSale\Filament\Pos\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Throwable;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Services\ReceiptBuilder;
use Webkul\PointOfSale\Services\RefundProcessor;
use Webkul\PointOfSale\Support\PosAccess;

class Orders extends Page
{
    use WithPagination;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $slug = '{session}/orders';

    protected static ?int $navigationSort = 2;

    protected string $view = 'point-of-sale::filament.pos.pages.orders';

    public ?Session $session = null;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = 'active';

    public ?int $selectedOrderId = null;

    public int $perPage = 20;

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/pos/pages/orders.navigation.label');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Registers::currentSession()?->orders()->count() ?? 0;

        return $count > 0 ? (string) $count : null;
    }

    public function mount(?Session $session = null): void
    {
        $this->session = $session ?? Registers::currentSession();

        abort_unless($this->session === null || PosAccess::reachesActiveSession($this->session), 403);
    }

    public static function getNavigationUrl(array $parameters = []): string
    {
        $session = Registers::currentSession();

        return $session
            ? static::getUrl(['session' => $session->getKey()])
            : Registers::getUrl();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) Registers::currentSession();
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/pos/pages/orders.title');
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    /**
     * @return array<string, string>
     */
    public function statusOptions(): array
    {
        $prefix = 'point-of-sale::filament/pos/pages/orders.status.';

        return array_merge(
            ['active' => __($prefix.'active')],
            collect(OrderState::cases())
                ->mapWithKeys(fn (OrderState $state): array => [$state->value => $state->getLabel()])
                ->all(),
        );
    }

    public function getOrders(): LengthAwarePaginator
    {
        if (! $this->session) {
            return Order::query()->whereRaw('1 = 0')->paginate($this->perPage);
        }

        $search = trim($this->search);

        $isRestaurant = $this->isRestaurant();

        return Order::withoutGlobalScopes()
            ->where('session_id', $this->session->getKey())
            ->with(['partner', 'user', 'currency'])
            ->when($isRestaurant, fn (Builder $query) => $query->with('table.floor'))
            ->when(
                $this->status === 'active',
                fn (Builder $query) => $query->whereNot('state', OrderState::CANCELED),
                fn (Builder $query) => $query->where('state', $this->status),
            )
            ->when($search !== '', fn (Builder $query) => $query->where(
                fn (Builder $matches) => $matches
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('receipt_code', 'like', "%{$search}%")
                    ->orWhereHas('partner', fn (Builder $partner) => $partner->where('name', 'like', "%{$search}%"))
                    ->when($isRestaurant, fn (Builder $matches) => $matches->orWhereHas(
                        'table',
                        fn (Builder $table) => $table
                            ->where('table_number', 'like', "%{$search}%")
                            ->orWhereHas('floor', fn (Builder $floor) => $floor->where('name', 'like', "%{$search}%")),
                    )),
            ))
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    public function isRestaurant(): bool
    {
        return (bool) $this->session?->config?->is_restaurant;
    }

    public function selectOrder(int $orderId): void
    {
        $this->selectedOrderId = $this->selectedOrderId === $orderId ? null : $orderId;

        $this->dispatchRefundQuantities();
    }

    protected function dispatchRefundQuantities(): void
    {
        $this->dispatch('pos-refund-lines', quantities: (object) $this->refundQuantities($this->getSelectedOrder()));
    }

    public function getSelectedOrder(): ?Order
    {
        if (! $this->selectedOrderId) {
            return null;
        }

        if (! $this->session) {
            return null;
        }

        return Order::withoutGlobalScopes()
            ->where('session_id', $this->session->getKey())
            ->with(['lines.product', 'payments.paymentMethod', 'partner', 'currency'])
            ->find($this->selectedOrderId);
    }

    public function money(float $amount, ?Order $order = null): string
    {
        return money($amount, $order?->currency?->name ?? $this->session?->config?->currency?->name);
    }

    public function invoiceOrderAction(): Action
    {
        $prefix = 'point-of-sale::filament/pos/pages/orders.actions.invoice.';

        return Action::make('invoiceOrder')
            ->label(__($prefix.'label'))
            ->icon('heroicon-o-document-text')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading(__($prefix.'heading'))
            ->action(function (array $arguments) use ($prefix): void {
                $order = Order::withoutGlobalScopes()
                    ->where('session_id', $this->session?->getKey())
                    ->find($arguments['order'] ?? null);

                if (! $order) {
                    return;
                }

                try {
                    PointOfSale::invoiceOrder($order);

                    Notification::make()
                        ->success()
                        ->title(__($prefix.'notification.title'))
                        ->send();
                } catch (Throwable $exception) {
                    Notification::make()
                        ->danger()
                        ->body($exception->getMessage())
                        ->send();
                }
            });
    }

    public function cancelOrderAction(): Action
    {
        $prefix = 'point-of-sale::filament/pos/pages/orders.actions.cancel.';

        return Action::make('cancelOrder')
            ->label(__($prefix.'label'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(__($prefix.'heading'))
            ->modalDescription(__($prefix.'description'))
            ->action(function (array $arguments) use ($prefix): void {
                $order = Order::withoutGlobalScopes()
                    ->where('session_id', $this->session?->getKey())
                    ->find($arguments['order'] ?? null);

                if (! $order || ! $this->canCancel($order)) {
                    return;
                }

                try {
                    PointOfSale::cancelOrder($order);

                    Notification::make()
                        ->success()
                        ->title(__($prefix.'notification.title'))
                        ->body(__($prefix.'notification.body', ['order' => $order->reference]))
                        ->send();
                } catch (Throwable $exception) {
                    Notification::make()
                        ->danger()
                        ->body($exception->getMessage())
                        ->send();
                }

                $this->dispatchRefundQuantities();
            });
    }

    public function backUrl(): string
    {
        return Register::getUrl(['config' => $this->session?->config_id]);
    }

    public function lineImage(OrderLine $line): ?string
    {
        if (! $this->session?->config?->show_product_images) {
            return null;
        }

        $image = collect($line->product?->images)->first();

        return $image ? Storage::url($image) : null;
    }

    public function receipt(Order $order): array
    {
        return app(ReceiptBuilder::class)->build($order);
    }

    public function detailsUrl(Order $order): string
    {
        return OrderResource::getUrl('view', ['record' => $order->getKey()], panel: 'admin');
    }

    /**
     * @return array<int, array{qty: float, refundable: float}>
     */
    public function refundQuantities(?Order $order): array
    {
        if (! $order) {
            return [];
        }

        return collect(app(RefundProcessor::class)->refundableLines($order))
            ->map(fn (float $refundable): array => ['qty' => 0, 'refundable' => $refundable])
            ->all();
    }

    public function refundOrder(array $quantities): void
    {
        $order = $this->getSelectedOrder();

        if (! $order || ! $this->session) {
            return;
        }

        try {
            $pending = app(RefundProcessor::class)->pendingRefund($order, $quantities);
        } catch (Throwable $exception) {
            Notification::make()
                ->danger()
                ->body($exception->getMessage())
                ->send();

            $this->dispatchRefundQuantities();

            return;
        }

        session()->put(Terminal::pendingRefundKey($this->session), $pending);

        $this->redirect(Register::getUrl(['config' => $this->session->config_id]), navigate: FilamentView::hasSpaMode());
    }

    public function canRefund(Order $order): bool
    {
        return filled(app(RefundProcessor::class)->refundableLines($order));
    }

    public function canCancel(Order $order): bool
    {
        return $order->state === OrderState::DRAFT
            && $order->session?->state !== SessionState::CLOSED;
    }

    public function canInvoice(Order $order): bool
    {
        return $order->state->isSettled()
            && ! $order->is_invoiced
            && $order->session?->state !== SessionState::CLOSED;
    }
}
