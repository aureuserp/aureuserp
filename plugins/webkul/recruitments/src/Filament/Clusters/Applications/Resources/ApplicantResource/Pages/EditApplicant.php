<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Recruitment\Enums\ApplicationStatus;
use Webkul\Recruitment\Enums\RecruitmentState;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource;
use Webkul\Recruitment\Mail\ApplicantRefuseMail;
use Webkul\Recruitment\Mail\ApplicationConfirmMail;
use Webkul\Recruitment\Mail\InterviewerAssignedMail;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Recruitment\Models\RefuseReason;
use Webkul\Security\Models\User;
use Webkul\Support\Services\EmailService;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditApplicant extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ApplicantResource::class;

    protected array $notificationData = [];

    protected array $interviewerChanges = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.notification.title'))
            ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.notification.body'));
    }

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
                ->action(function (Applicant $record, $data) {
                    $record->update($data);
                    $record->refresh();

                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.state.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.state.notification.body'))
                        ->send();
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
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.delete.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.delete.notification.body'))
                ),
            ForceDeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.force-delete.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.force-delete.notification.body'))
                ),
            RestoreAction::make()
                ->successNotification(
                    Notification::make()
                        ->info()
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.restore.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.restore.notification.body'))
                ),
            Action::make('refuse')
                ->modalIcon('heroicon-s-bug-ant')
                ->hidden(fn ($record) => $record->refuse_reason_id || $record->application_status->value === ApplicationStatus::ARCHIVED->value)
                ->modalHeading(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.refuse.title'))
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
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.refuse.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.refuse.notification.body'))
                        ->send();
                }),
            Action::make('restore')
                ->hidden(fn ($record) => ! $record->refuse_reason_id)
                ->modalHeading(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.reopen.title'))
                ->requiresConfirmation()
                ->color('gray')
                ->action(function (Applicant $record) {
                    $record->reopen();

                    Notification::make()
                        ->info()
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.reopen.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.header-actions.reopen.notification.body'))
                        ->send();
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->record->load('interviewer');
        $oldData = $record->getRawOriginal();

        if (isset($data['recruiter_id']) && $data['recruiter_id'] !== $oldData['recruiter_id']) {
            $data['date_opened'] = now();
        }

        if (isset($data['stage_id']) && empty($oldData['stage_id'])) {
            $data['date_last_stage_updated'] = now();
            $this->notificationData = $data;
        } elseif (isset($data['stage_id']) && $data['stage_id'] !== $oldData['stage_id']) {
            $data['date_last_stage_updated'] = now();
            $data['last_stage_id'] = $oldData['stage_id'];
        }

        if (isset($data['recruitments_applicant_interviewers']) && is_array($data['recruitments_applicant_interviewers'])) {
            $oldInterviewers = collect($record->interviewer->pluck('id'));
            $newInterviewers = collect($data['recruitments_applicant_interviewers']);

            if (! $oldInterviewers->isEmpty() || ! $newInterviewers->isEmpty()) {
                $this->interviewerChanges = [
                    'old' => $oldInterviewers,
                    'new' => $newInterviewers,
                ];
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if (! empty($this->notificationData)) {
            $this->sendApplicationConfirmationNotification();
        }

        if (! empty($this->interviewerChanges)) {
            $this->record->interviewer()->sync($this->interviewerChanges['new']);

            $this->sendInterviewerAssignmentNotification();
        }
    }

    protected function sendApplicationConfirmationNotification(): void
    {
        $data = $this->prepareCandidateNotificationPayload();

        app(EmailService::class)->send(
            mailClass: ApplicationConfirmMail::class,
            view: $viewName = 'recruitments::mails.application-confirm',
            payload: $data
        );

        $messageData = [
            'from' => [
                'company' => Auth::user()->defaultCompany->toArray(),
            ],
            'body' => view($viewName, ['payload' => $data])->render(),
            'type' => 'comment',
        ];

        $this->record->addMessage($messageData, Auth::user()->id);
    }

    protected function sendInterviewerAssignmentNotification(): void
    {
        $oldInterviewers = collect($this->interviewerChanges['old']);
        $newInterviewers = collect($this->interviewerChanges['new']);

        $addedInterviewers = $newInterviewers->diff($oldInterviewers);

        $addedInterviewers = $addedInterviewers->reject(fn ($id) => $id == Auth::id());

        if ($addedInterviewers->isNotEmpty()) {
            foreach ($addedInterviewers as $interviewerId) {
                $interviewer = User::find($interviewerId);

                if ($interviewer) {
                    $data = $this->prepareInterviewerNotificationPayload($interviewer);

                    app(EmailService::class)->send(
                        mailClass: InterviewerAssignedMail::class,
                        view: 'recruitments::mails.interviewer-assigned',
                        payload: $data
                    );
                }
            }
        }
    }

    private function prepareCandidateNotificationPayload(): array
    {
        return [
            'record_name'  => $this->record->candidate->name,
            'job_position' => $jobPosition = $this->record->job?->name,
            'subject'      => __('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.mail.application-confirm.subject', [
                'job_position' => $jobPosition,
            ]),
            'to' => [
                'address' => $this->record->candidate->email_from,
                'name'    => $this->record->candidate->name,
            ],
        ];
    }

    private function prepareInterviewerNotificationPayload($interviewer): array
    {
        return [
            'record_name' => $candidateName = $this->record->candidate->name,
            'record_url'  => $this->getRedirectUrl(),
            'subject'     => __('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.mail.interviewer-assigned.subject', [
                'applicant' => $candidateName,
            ]),
            'to' => [
                'address' => $interviewer->email,
                'name'    => $interviewer->name,
            ],
        ];
    }

    private function prepareApplicantRefuseNotificationPayload(array $data): array
    {
        return [
            'applicant_name' => $this->record->candidate->name,
            'subject'        => __('recruitments::filament/clusters/applications/resources/applicant/pages/edit-applicant.mail.application-refused.subject', [
                'application' => $this->record->job?->name,
            ]),
            'to' => [
                'address' => $data['email'] ?? $this->record?->candidate?->email_from,
                'name'    => $this->record?->candidate?->name,
            ],
        ];
    }
}
