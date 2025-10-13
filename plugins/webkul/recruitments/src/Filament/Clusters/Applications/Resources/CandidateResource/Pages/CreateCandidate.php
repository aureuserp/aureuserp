<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource;

class CreateCandidate extends CreateRecord
{
    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = CandidateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('recruitments::filament/clusters/applications/resources/candidate/pages/create-candidate.notification.title'))
            ->body(__('recruitments::filament/clusters/applications/resources/candidate/pages/create-candidate.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $data['creator_id'] = $user->id;
        $data['company_id'] = $user?->default_company_id;

        return $data;
    }
}
