<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategoryResource;

class ListApplicantCategories extends ListRecords
{
    protected static string $resource = ApplicantCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('recruitments::filament/clusters/configurations/resources/applicant-category/pages/list-applicant-categories.header-actions.create.label'))
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/configurations/resources/applicant-category/pages/list-applicant-categories.notification.title'))
                        ->body(__('recruitments::filament/clusters/configurations/resources/applicant-category/pages/list-applicant-categories.notification.body'))
                ),
        ];
    }
}
