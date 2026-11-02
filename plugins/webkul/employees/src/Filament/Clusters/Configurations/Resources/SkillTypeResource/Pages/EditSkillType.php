<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditSkillType extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = SkillTypeResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('employees::filament/clusters/configurations/resources/skill-type/pages/edit-skill-type.notification.title'))
            ->body(__('employees::filament/clusters/configurations/resources/skill-type/pages/edit-skill-type.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('employees::filament/clusters/configurations/resources/skill-type/pages/edit-skill-type.header-actions.delete.notification.title'))
                        ->body(__('employees::filament/clusters/configurations/resources/skill-type/pages/edit-skill-type.header-actions.delete.notification.body')),
                ),
        ];
    }
}
