<?php

namespace Webkul\Crm\Filament\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Services\LeadScoring;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageLeadScoring extends SettingsPage
{
    use HasCRMClusterNavigation, HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $slug = 'crm/manage-lead-scoring';

    protected static string|UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 2;

    protected static string $settings = CRMSettings::class;

    protected static ?string $cluster = Settings::class;

    protected bool $scoringChanged = false;

    protected static function getPagePermission(): ?string
    {
        return 'page_crm_manage_lead_scoring';
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('crm::filament/clusters/settings/pages/manage-lead-scoring.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-lead-scoring.title');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('crm::filament/clusters/settings/pages/manage-lead-scoring.description');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-lead-scoring.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                CheckboxList::make('scoring_variables')
                    ->label(__('crm::filament/clusters/settings/pages/manage-lead-scoring.form.variables'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-lead-scoring.form.variables-helper'))
                    ->options([
                        'country_id'    => __('crm::filament/lead-scoring.variables.country_id'),
                        'state_id'      => __('crm::filament/lead-scoring.variables.state_id'),
                        'phone'         => __('crm::filament/lead-scoring.variables.phone'),
                        'email'         => __('crm::filament/lead-scoring.variables.email'),
                        'utm_source_id' => __('crm::filament/lead-scoring.variables.utm_source_id'),
                        'tags'          => __('crm::filament/lead-scoring.variables.tags'),
                    ])
                    ->columns(2),
                DatePicker::make('scoring_start_date')
                    ->label(__('crm::filament/clusters/settings/pages/manage-lead-scoring.form.start-date'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-lead-scoring.form.start-date-helper'))
                    ->maxDate(now()),
                Actions::make([
                    Action::make('updateProbabilities')
                        ->label(__('crm::filament/clusters/settings/pages/manage-lead-scoring.form.update'))
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->modalDescription(__('crm::filament/clusters/settings/pages/manage-lead-scoring.form.update-description'))
                        ->action(function (Get $get): void {
                            $settings = app(CRMSettings::class);
                            $settings->scoring_variables = array_values(array_intersect(LeadScoring::VARIABLES, (array) $get('scoring_variables')));
                            $settings->scoring_start_date = filled($get('scoring_start_date')) ? (string) $get('scoring_start_date') : null;
                            $settings->save();

                            $count = app(LeadScoring::class)->rebuildAndRescore();

                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/settings/pages/manage-lead-scoring.form.updated', ['count' => $count]))
                                ->send();
                        }),
                ]),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $current = app(CRMSettings::class);

        $data['scoring_variables'] = array_values(array_intersect(LeadScoring::VARIABLES, (array) ($data['scoring_variables'] ?? [])));
        $data['scoring_start_date'] = filled($data['scoring_start_date'] ?? null) ? (string) $data['scoring_start_date'] : null;

        $this->scoringChanged = $data['scoring_variables'] !== array_values((array) $current->scoring_variables)
            || $data['scoring_start_date'] !== $current->scoring_start_date;

        return $data;
    }

    protected function afterSave(): void
    {
        if (! $this->scoringChanged) {
            return;
        }

        $count = app(LeadScoring::class)->rebuildAndRescore();

        Notification::make()
            ->success()
            ->title(__('crm::filament/clusters/settings/pages/manage-lead-scoring.form.updated', ['count' => $count]))
            ->send();
    }
}
