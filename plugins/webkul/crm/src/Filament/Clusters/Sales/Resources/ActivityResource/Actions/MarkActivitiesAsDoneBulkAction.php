<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Models\Activity;

class MarkActivitiesAsDoneBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'markActivitiesAsDone';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/activity.actions.done.label'))
            ->icon('heroicon-o-check-circle')
            ->modalHeading(__('crm::filament/clusters/sales/resources/activity.actions.done.modal.heading'))
            ->modalSubmitActionLabel(__('crm::filament/clusters/sales/resources/activity.actions.done.modal.submit'))
            ->deselectRecordsAfterCompletion()
            ->schema([
                Textarea::make('feedback')
                    ->label(__('crm::filament/clusters/sales/resources/activity.actions.done.form.feedback'))
                    ->rows(3),
            ])
            ->action(function (Collection $records, array $data): void {
                $activities = $records->filter(fn (Activity $activity): bool => ActivityResource::canEdit($activity));

                $activities->each(fn (Activity $activity) => $activity->markAsDone($data['feedback'] ?? null));

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/activity.actions.done.bulk-notification', ['count' => $activities->count()]))
                    ->send();
            });
    }
}
