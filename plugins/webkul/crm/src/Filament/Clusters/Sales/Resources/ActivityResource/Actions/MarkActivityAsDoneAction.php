<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Models\Activity;

class MarkActivityAsDoneAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'markActivityAsDone';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/activity.actions.done.label'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->modalHeading(__('crm::filament/clusters/sales/resources/activity.actions.done.modal.heading'))
            ->modalSubmitActionLabel(__('crm::filament/clusters/sales/resources/activity.actions.done.modal.submit'))
            ->visible(fn (Activity $record): bool => ActivityResource::canEdit($record))
            ->schema([
                Textarea::make('feedback')
                    ->label(__('crm::filament/clusters/sales/resources/activity.actions.done.form.feedback'))
                    ->rows(3),
            ])
            ->action(function (Activity $record, array $data): void {
                $record->markAsDone($data['feedback'] ?? null);

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/activity.actions.done.notification.title'))
                    ->body(__('crm::filament/clusters/sales/resources/activity.actions.done.notification.body'))
                    ->send();
            });
    }
}
