<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Services\LeadScoring;

class CreateLead extends CreateRecord
{
    use HasCrmClusterNavigation;

    protected static string $resource = LeadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = Lead::TYPE_LEAD;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return LeadResource::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('crm::filament/clusters/leads/resources/lead/pages/create-lead.notification.title'))
            ->body(__('crm::filament/clusters/leads/resources/lead/pages/create-lead.notification.body'));
    }

    protected function afterCreate(): void
    {
        app(LeadScoring::class)->refresh($this->getRecord()->load('tags'));
    }
}
