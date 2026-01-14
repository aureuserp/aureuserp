<?php

namespace Webkul\Project\Filament\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use UnitEnum;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource;
use Webkul\Project\Settings\TaskSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageTasks extends SettingsPage
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|UnitEnum|null $navigationGroup = 'Project';

    protected static string $settings = TaskSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('projects::filament/clusters/settings/pages/manage-tasks.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('projects::filament/clusters/settings/pages/manage-tasks.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/settings/pages/manage-tasks.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('enable_project_stages')
                    ->label(__('projects::filament/clusters/settings/pages/manage-tasks.form.enable-project-stages'))
                    ->required()
                    ->helperText(new HtmlString(__('projects::filament/clusters/settings/pages/manage-tasks.form.enable-milestones-helper-text').'</br><a href="'.TaskStageResource::getUrl().'" class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-md fi-link-size-md gap-1.5 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action"><svg style="--c-400:var(--primary-400);--c-600:var(--primary-600)" class="fi-link-icon h-5 w-5 text-custom-600 dark:text-custom-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path></svg><span class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--primary-400);--c-600:var(--primary-600)">'.__('projects::filament/clusters/settings/pages/manage-tasks.form.configure-stages').'</span></a>')),

                Toggle::make('enable_milestones')
                    ->label(__('projects::filament/clusters/settings/pages/manage-tasks.form.enable-milestones'))
                    ->helperText(__('projects::filament/clusters/settings/pages/manage-tasks.form.enable-milestones-helper-text'))
                    ->required(),
            ]);
    }
}
