<?php

namespace Webkul\Crm\Filament\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use UnitEnum;
use Webkul\Crm\Console\Commands\AssignLeads;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Services\LeadAssignment;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageCRM extends SettingsPage
{
    use HasCRMClusterNavigation, HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $slug = 'crm/manage-crm';

    protected static string|UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    protected static string $settings = CRMSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_crm_manage_c_r_m';
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('crm::filament/clusters/settings/pages/manage-crm.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-crm.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-crm.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('assignNow')
                ->label(__('crm::filament/clusters/settings/pages/manage-crm.actions.assign-now.label'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->visible(fn (): bool => app(CRMSettings::class)->use_auto_assignment)
                ->requiresConfirmation()
                ->modalDescription(__('crm::filament/clusters/settings/pages/manage-crm.actions.assign-now.description'))
                ->action(function (): void {
                    $result = app(LeadAssignment::class)->run();

                    Notification::make()
                        ->success()
                        ->title(__('crm::filament/clusters/settings/pages/manage-crm.actions.assign-now.notification', $result))
                        ->send();
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('use_leads')
                    ->label(__('crm::filament/clusters/settings/pages/manage-crm.form.use-leads.label'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-crm.form.use-leads.helper')),
                Toggle::make('use_recurring_revenues')
                    ->label(__('crm::filament/clusters/settings/pages/manage-crm.form.recurring-revenues.label'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-crm.form.recurring-revenues.helper')),
                Toggle::make('multi_teams')
                    ->label(__('crm::filament/clusters/settings/pages/manage-crm.form.multi-teams.label'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-crm.form.multi-teams.helper')),
                Toggle::make('use_auto_assignment')
                    ->label(__('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.label'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.helper'))
                    ->live(),
                Select::make('auto_assignment_action')
                    ->label(__('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.running'))
                    ->options([
                        'manual' => __('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.manual'),
                        'auto'   => __('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.auto'),
                    ])
                    ->selectablePlaceholder(false)
                    ->required()
                    ->live()
                    ->visible(fn (Get $get): bool => (bool) $get('use_auto_assignment')),
                Grid::make(2)
                    ->visible(fn (Get $get): bool => (bool) $get('use_auto_assignment') && $get('auto_assignment_action') === 'auto')
                    ->schema([
                        TextInput::make('auto_assignment_interval_number')
                            ->label(__('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.repeat-every'))
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->required(),
                        Select::make('auto_assignment_interval_type')
                            ->label(__('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.unit'))
                            ->options([
                                'minutes' => __('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.units.minutes'),
                                'hours'   => __('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.units.hours'),
                                'days'    => __('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.units.days'),
                                'weeks'   => __('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.units.weeks'),
                            ])
                            ->selectablePlaceholder(false)
                            ->required(),
                    ]),
                Text::make(fn (): string => filled($runAt = app(CRMSettings::class)->auto_assignment_run_at)
                    ? __('crm::filament/clusters/settings/pages/manage-crm.form.auto-assignment.next-run', ['date' => Carbon::parse($runAt)->timezone(config('app.timezone'))->isoFormat('LLL')])
                    : '')
                    ->visible(fn (Get $get): bool => (bool) $get('use_auto_assignment') && $get('auto_assignment_action') === 'auto'),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $current = app(CRMSettings::class);

        $isRepeated = ($data['use_auto_assignment'] ?? false) && ($data['auto_assignment_action'] ?? 'manual') === 'auto';

        if (! $isRepeated) {
            $data['auto_assignment_run_at'] = null;

            return $data;
        }

        $intervalChanged = (int) ($data['auto_assignment_interval_number'] ?? 1) !== $current->auto_assignment_interval_number
            || ($data['auto_assignment_interval_type'] ?? 'days') !== $current->auto_assignment_interval_type;

        if (blank($current->auto_assignment_run_at) || $intervalChanged) {
            $settings = clone $current;
            $settings->auto_assignment_interval_number = (int) ($data['auto_assignment_interval_number'] ?? 1);
            $settings->auto_assignment_interval_type = (string) ($data['auto_assignment_interval_type'] ?? 'days');

            $data['auto_assignment_run_at'] = AssignLeads::nextRun($settings)->toIso8601String();
        } else {
            $data['auto_assignment_run_at'] = $current->auto_assignment_run_at;
        }

        return $data;
    }
}
