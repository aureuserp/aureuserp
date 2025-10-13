<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditPartner extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PartnerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('partners::filament/resources/partner/pages/edit-partner.title');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('partners::filament/resources/partner/pages/edit-partner.notification.title'))
            ->body(__('partners::filament/resources/partner/pages/edit-partner.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->setResource(static::$resource),
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('partners::filament/resources/partner/pages/edit-partner.header-actions.delete.notification.title'))
                        ->body(__('partners::filament/resources/partner/pages/edit-partner.header-actions.delete.notification.body')),
                ),
        ];
    }
}
