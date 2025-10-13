<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource;
use Webkul\Recruitment\Models\Candidate;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewCandidate extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('gotoEmployee')
                ->tooltip(__('recruitments::filament/clusters/applications/resources/candidate/pages/view-candidate.goto-employee-tooltip'))
                ->visible(fn ($record) => $record->employee_id)
                ->icon('heroicon-s-arrow-top-right-on-square')
                ->iconButton()
                ->action(function (Candidate $record) {
                    $employee = $record->createEmployee();

                    return redirect(EmployeeResource::getUrl('view', ['record' => $employee]));
                }),
            Action::make('createEmployee')
                ->label(__('recruitments::filament/clusters/applications/resources/candidate/pages/view-candidate.create-employee'))
                ->hidden(fn ($record) => $record->employee_id)
                ->action(function (Candidate $record) {
                    $employee = $record->createEmployee();

                    return redirect(EmployeeResource::getUrl('edit', ['record' => $employee]));
                }),
            ChatterActions\ChatterAction::make()
                ->setResource(static::$resource),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/applications/resources/candidate/pages/view-candidate.header-actions.delete.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/candidate/pages/view-candidate.header-actions.delete.notification.body'))
                ),
        ];
    }
}
