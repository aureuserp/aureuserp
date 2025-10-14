<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Recruitment\Enums\ApplicationStatus;
use Webkul\Recruitment\Enums\RecruitmentState;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource;
use Webkul\Recruitment\Mail\ApplicantRefuseMail;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Recruitment\Models\RefuseReason;
use Webkul\Support\Services\EmailService;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewApplicant extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('state')
                ->hiddenLabel()
                ->icon(function ($record) {
                    if ($record->state == RecruitmentState::DONE->value) {
                        return RecruitmentState::DONE->getIcon();
                    } elseif ($record->state == RecruitmentState::BLOCKED->value) {
                        return RecruitmentState::BLOCKED->getIcon();
                    } elseif ($record->state == RecruitmentState::NORMAL->value) {
                        return RecruitmentState::NORMAL->getIcon();
                    }
                })
                ->iconButton()
                ->color(function ($record) {
                    if ($record->state == RecruitmentState::DONE->value) {
                        return RecruitmentState::DONE->getColor();
                    } elseif ($record->state == RecruitmentState::BLOCKED->value) {
                        return RecruitmentState::BLOCKED->getColor();
                    } elseif ($record->state == RecruitmentState::NORMAL->value) {
                        return RecruitmentState::NORMAL->getColor();
                    }
                })
                ->schema([
                    ToggleButtons::make('state')
                        ->inline()
                        ->options(RecruitmentState::class),
                ])
                ->fillForm(fn ($record) => [
                    'state' => $record->state,
                ])
                ->tooltip(function ($record) {
                    if ($record->state == RecruitmentState::DONE->value) {
                        return RecruitmentState::DONE->getLabel();
                    } elseif ($record->state == RecruitmentState::BLOCKED->value) {
                        return RecruitmentState::BLOCKED->getLabel();
                    } elseif ($record->state == RecruitmentState::NORMAL->value) {
                        return RecruitmentState::NORMAL->getLabel();
                    }
                })
                ->action(function (Applicant $record, $data, $livewire) {
                    $record->update($data);

                    // Force refresh the record in the livewire component
                    $livewire->record = $record->fresh();

                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/view-applicant.header-actions.state.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/view-applicant.header-actions.state.notification.body'))
                        ->send();

                    // Force complete refresh
                    $livewire->dispatch('$refresh');
                }),
            Action::make('gotoEmployee')
                ->tooltip(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.goto-employee'))
                ->visible(fn ($record) => $record->application_status->value == ApplicationStatus::HIRED->value || $record->candidate->employee_id)
                ->icon('heroicon-s-arrow-top-right-on-square')
                ->iconButton()
                ->action(function (Applicant $record) {
                    $employee = $record->createEmployee();

                    return redirect(EmployeeResource::getUrl('view', ['record' => $employee]));
                }),
            ChatterActions\ChatterAction::make()
                ->setResource(static::$resource),
            Action::make('createEmployee')
                ->label(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.create-employee'))
                ->hidden(fn ($record) => $record->application_status->value == ApplicationStatus::HIRED->value || $record->candidate->employee_id)
                ->action(function (Applicant $record) {
                    $employee = $record->createEmployee();

                    return redirect(EmployeeResource::getUrl('edit', ['record' => $employee]));
                }),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/view-applicant.header-actions.delete.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/view-applicant.header-actions.delete.notification.body'))
                ),
            Action::make('Refuse')
                ->modalIcon('heroicon-s-bug-ant')
                ->hidden(fn ($record) => $record->refuse_reason_id || $record->application_status->value === ApplicationStatus::ARCHIVED->value)
                ->modalHeading('Refuse Reason')
                ->schema(function (Schema $schema, $record) {
                    return $schema->components([
                        ToggleButtons::make('refuse_reason_id')
                            ->hiddenLabel()
                            ->inline()
                            ->live()
                            ->options(RefuseReason::all()->pluck('name', 'id')),
                        Toggle::make('notify')
                            ->inline()
                            ->live()
                            ->default(true)
                            ->visible(fn (Get $get) => $get('refuse_reason_id'))
                            ->label('Notify'),
                        TextInput::make('email')
                            ->visible(fn (Get $get) => $get('notify') && $get('refuse_reason_id'))
                            ->default($record->candidate->email_from)
                            ->label('Email To'),
                    ]);
                })
                ->action(function (array $data, Applicant $record) {
                    $refuseReason = RefuseReason::find($data['refuse_reason_id']);

                    if (! $refuseReason) {
                        return null;
                    }

                    $record->setAsRefused($refuseReason?->id);

                    if (isset($data['notify']) && $data['notify']) {
                        $data = $this->prepareApplicantRefuseNotificationPayload($data);

                        app(EmailService::class)->send(
                            mailClass: ApplicantRefuseMail::class,
                            view: "recruitments::mails.{$refuseReason?->template}",
                            payload: $data
                        );
                    }

                    Notification::make()
                        ->info()
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/view-applicant.header-actions.refuse.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/view-applicant.header-actions.refuse.notification.body'))
                        ->send();
                }),
            Action::make('Restore')
                ->hidden(fn ($record) => ! $record->refuse_reason_id)
                ->modalHeading('Restore Applicant from refuse')
                ->requiresConfirmation()
                ->color('gray')
                ->action(function (Applicant $record) {
                    $record->reopen();

                    Notification::make()
                        ->info()
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/view-applicant.header-actions.reopen.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/view-applicant.header-actions.reopen.notification.body'))
                        ->send();
                }),

        ];
    }

    private function prepareApplicantRefuseNotificationPayload(array $data): array
    {
        return [
            'applicant_name' => $this->record->candidate->name,
            'subject'        => __('recruitments::filament/clusters/applications/resources/applicant/pages/view-applicant.mail.application-refused.subject', [
                'application' => $this->record->job?->name,
            ]),
            'to' => [
                'address' => $data['email'] ?? $this->record?->candidate?->email_from,
                'name'    => $this->record?->candidate?->name,
            ],
        ];
    }
}
