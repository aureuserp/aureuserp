<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Models\Activity;

class SnoozeActivityAction extends Action
{
    public const DAYS = 7;

    public static function getDefaultName(): ?string
    {
        return 'snoozeActivity';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/activity.actions.snooze.label', ['days' => static::DAYS]))
            ->icon('heroicon-o-bell-snooze')
            ->color('gray')
            ->visible(fn (Activity $record): bool => ActivityResource::canEdit($record))
            ->action(function (Activity $record): void {
                $from = $record->date_deadline?->isFuture() ? $record->date_deadline : today();

                $record->update(['date_deadline' => $from->copy()->addDays(static::DAYS)]);

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/activity.actions.snooze.notification', ['date' => $record->date_deadline->toFormattedDateString()]))
                    ->send();
            });
    }
}
