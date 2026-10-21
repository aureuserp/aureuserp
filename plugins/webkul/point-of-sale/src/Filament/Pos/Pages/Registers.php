<?php

namespace Webkul\PointOfSale\Filament\Pos\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Facades\FilamentView;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Throwable;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Session;

class Registers extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $slug = 'registers';

    protected static ?int $navigationSort = 0;

    protected string $view = 'point-of-sale::filament.pos.pages.registers';

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/pos/pages/registers.navigation.label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return ! static::ownSession();
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/pos/pages/registers.title');
    }

    public function getRegisters(): Collection
    {
        return Config::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();
    }

    public function liveSessionFor(Config $config): ?Session
    {
        return PointOfSale::liveSessionFor($config);
    }

    public function isDiscardable(Session $session): bool
    {
        return PointOfSale::isSessionDiscardable($session);
    }

    public function discardSessionAction(): Action
    {
        $prefix = 'point-of-sale::filament/pos/pages/registers.actions.discard.';

        return Action::make('discardSession')
            ->label(__($prefix.'label'))
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->iconButton()
            ->tooltip(__($prefix.'label'))
            ->requiresConfirmation()
            ->modalHeading(__($prefix.'heading'))
            ->modalDescription(__($prefix.'description'))
            ->modalSubmitActionLabel(__($prefix.'confirm'))
            ->action(function (array $arguments) use ($prefix): void {
                $session = Session::find($arguments['session'] ?? null);

                if (! $session) {
                    return;
                }

                try {
                    PointOfSale::discardSession($session);

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

    public function openRegister(int $configId, bool $newTab = false): ?string
    {
        $config = Config::find($configId);

        if (! $config) {
            return null;
        }

        $url = Register::getUrl(['config' => $config->getKey()]);

        if ($newTab) {
            return $url;
        }

        $this->redirect($url, navigate: FilamentView::hasSpaMode());

        return null;
    }

    public static function currentSession(): ?Session
    {
        return once(fn (): ?Session => static::routeSession() ?? static::ownSession());
    }

    public static function routeSession(): ?Session
    {
        return once(function (): ?Session {
            $sessionId = rescue(
                fn () => Route::getRoutes()->match(Request::create(Livewire::originalUrl()))->parameter('session'),
                report: false,
            );

            $session = filled($sessionId) ? Session::find($sessionId) : null;

            return $session?->isLive() ? $session : null;
        });
    }

    /**
     * Only an unambiguous session is resumed automatically: exactly one open
     * register belonging to this cashier. Zero or several means the cashier
     * picks, so nobody is dropped into a till that is not theirs.
     */
    public static function ownSession(): ?Session
    {
        $sessions = Session::query()
            ->whereIn('state', [SessionState::OPENING_CONTROL, SessionState::OPENED])
            ->where('user_id', Auth::id())
            ->latest('id')
            ->limit(2)
            ->get();

        return $sessions->count() === 1 ? $sessions->first() : null;
    }
}
