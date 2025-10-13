<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource;

class EditAccrualPlan extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = AccrualPlanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('time-off::filament/clusters/configurations/resources/accrual-plan/pages/edit-accrual-plan.notification.title'))
            ->body(__('time-off::filament/clusters/configurations/resources/accrual-plan/pages/edit-accrual-plan.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/clusters/configurations/resources/accrual-plan/pages/edit-accrual-plan.header-actions.delete.notification.title'))
                        ->body(__('time-off::filament/clusters/configurations/resources/accrual-plan/pages/edit-accrual-plan.header-actions.delete.notification.body'))
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();

        $data['company_id'] = $user?->default_company_id;
        $data['creator_id'] = $user->id;

        return $data;
    }
}
