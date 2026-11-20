<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Models\Activity;
use Webkul\Security\Models\User;

class RescheduleActivityAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'rescheduleActivity';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/activity.actions.reschedule.label'))
            ->icon('heroicon-o-calendar')
            ->color('gray')
            ->modalHeading(__('crm::filament/clusters/sales/resources/activity.actions.reschedule.modal.heading'))
            ->modalWidth('lg')
            ->visible(fn (Activity $record): bool => ActivityResource::canEdit($record))
            ->fillForm(fn (Activity $record): array => [
                'date_deadline' => $record->date_deadline,
                'summary'       => $record->summary,
                'assigned_to'   => $record->assigned_to,
            ])
            ->schema([
                DatePicker::make('date_deadline')
                    ->label(__('crm::filament/clusters/sales/resources/activity.actions.reschedule.form.due-date'))
                    ->native(false)
                    ->required(),
                TextInput::make('summary')
                    ->label(__('crm::filament/clusters/sales/resources/activity.actions.reschedule.form.summary'))
                    ->maxLength(255),
                Select::make('assigned_to')
                    ->label(__('crm::filament/clusters/sales/resources/activity.actions.reschedule.form.assigned-to'))
                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
            ])
            ->action(function (Activity $record, array $data): void {
                $record->update($data);

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/activity.actions.reschedule.notification.title'))
                    ->body(__('crm::filament/clusters/sales/resources/activity.actions.reschedule.notification.body'))
                    ->send();
            });
    }
}
