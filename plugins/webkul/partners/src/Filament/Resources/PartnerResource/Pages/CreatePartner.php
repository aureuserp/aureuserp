<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Partner\Filament\Resources\PartnerResource;

class CreatePartner extends CreateRecord
{
    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = PartnerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    public function getTitle(): string|Htmlable
    {
        return __('partners::filament/resources/partner/pages/create-partner.title');
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('partners::filament/resources/partner/pages/create-partner.notification.title'))
            ->body(__('partners::filament/resources/partner/pages/create-partner.notification.body'));
    }
}
