<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Throwable;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Session;

class OpenRescueSessionAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'point-of-sale.session.open-rescue';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('point-of-sale::filament/admin/clusters/orders/actions/open-rescue-session.label'))
            ->icon('heroicon-o-lifebuoy')
            ->color('warning')
            ->requiresConfirmation()
            ->action(function (Session $record): void {
                try {
                    PointOfSale::openRescueSession($record);

                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/orders/actions/open-rescue-session.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/orders/actions/open-rescue-session.notification.success.body'))
                        ->send();
                } catch (Throwable $exception) {
                    Notification::make()
                        ->danger()
                        ->body($exception->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(fn (Session $record): bool => ! $record->isLive());
    }
}
