<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Webkul\Crm\Console\Commands\AssignLeads;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Services\LeadAssignment;
use Webkul\Crm\Services\LeadScoring;
use Webkul\Crm\Settings\CrmSettings;

class ManageSettings extends SettingsPage
{
    use HasCrmClusterNavigation, HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $slug = 'settings';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 0;

    protected static string $settings = CrmSettings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_crm_manage_settings';
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/pages/manage-settings.navigation.title');
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/configurations/pages/manage-settings.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('assignNow')
                ->label(__('crm::filament/clusters/configurations/pages/manage-settings.actions.assign-now.label'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->visible(fn (): bool => app(CrmSettings::class)->use_auto_assignment)
                ->requiresConfirmation()
                ->modalDescription(__('crm::filament/clusters/configurations/pages/manage-settings.actions.assign-now.description'))
                ->action(function (): void {
                    $result = app(LeadAssignment::class)->run();

                    Notification::make()
                        ->success()
                        ->title(__('crm::filament/clusters/configurations/pages/manage-settings.actions.assign-now.notification', $result))
                        ->send();
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        $label = fn (string $key): string => __('crm::filament/clusters/configurations/pages/manage-settings.form.'.$key);

        return $schema
            ->components([
                Section::make($label('sections.crm'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('use_leads')
                            ->label($label('use-leads.label'))
                            ->helperText($label('use-leads.helper')),
                        Toggle::make('use_recurring_revenues')
                            ->label($label('recurring-revenues.label'))
                            ->helperText($label('recurring-revenues.helper')),
                        Toggle::make('multi_teams')
                            ->label($label('multi-teams.label'))
                            ->helperText($label('multi-teams.helper')),
                        Grid::make(1)
                            ->schema([
                                Toggle::make('use_auto_assignment')
                                    ->label($label('auto-assignment.label'))
                                    ->helperText($label('auto-assignment.helper'))
                                    ->live(),
                                Select::make('auto_assignment_action')
                                    ->label($label('auto-assignment.running'))
                                    ->options([
                                        'manual' => $label('auto-assignment.manual'),
                                        'auto'   => $label('auto-assignment.auto'),
                                    ])
                                    ->selectablePlaceholder(false)
                                    ->required()
                                    ->live()
                                    ->visible(fn (Get $get): bool => (bool) $get('use_auto_assignment')),
                                Grid::make(2)
                                    ->visible(fn (Get $get): bool => (bool) $get('use_auto_assignment') && $get('auto_assignment_action') === 'auto')
                                    ->schema([
                                        TextInput::make('auto_assignment_interval_number')
                                            ->label($label('auto-assignment.repeat-every'))
                                            ->numeric()
                                            ->integer()
                                            ->minValue(1)
                                            ->required(),
                                        Select::make('auto_assignment_interval_type')
                                            ->label($label('auto-assignment.unit'))
                                            ->options([
                                                'minutes' => $label('auto-assignment.units.minutes'),
                                                'hours'   => $label('auto-assignment.units.hours'),
                                                'days'    => $label('auto-assignment.units.days'),
                                                'weeks'   => $label('auto-assignment.units.weeks'),
                                            ])
                                            ->selectablePlaceholder(false)
                                            ->required(),
                                    ]),
                                Text::make(fn (): string => filled($runAt = app(CrmSettings::class)->auto_assignment_run_at)
                                    ? __('crm::filament/clusters/configurations/pages/manage-settings.form.auto-assignment.next-run', ['date' => Carbon::parse($runAt)->timezone(config('app.timezone'))->isoFormat('LLL')])
                                    : '')
                                    ->visible(fn (Get $get): bool => (bool) $get('use_auto_assignment') && $get('auto_assignment_action') === 'auto'),
                            ]),
                    ]),
                Section::make($label('scoring.section'))
                    ->description($label('scoring.description'))
                    ->columns(2)
                    ->schema([
                        CheckboxList::make('scoring_variables')
                            ->label($label('scoring.variables'))
                            ->helperText($label('scoring.variables-helper'))
                            ->options(collect(LeadScoring::VARIABLES)->mapWithKeys(fn (string $variable): array => [$variable => __('crm::filament/lead-scoring.variables.'.$variable)])->all())
                            ->columns(2),
                        DatePicker::make('scoring_start_date')
                            ->label($label('scoring.start-date'))
                            ->helperText($label('scoring.start-date-helper'))
                            ->maxDate(now()),
                        Actions::make([
                            Action::make('updateProbabilities')
                                ->label($label('scoring.update'))
                                ->icon('heroicon-o-arrow-path')
                                ->requiresConfirmation()
                                ->modalDescription($label('scoring.update-description'))
                                ->action(function (Get $get): void {
                                    $settings = app(CrmSettings::class);
                                    $settings->scoring_variables = array_values(array_intersect(LeadScoring::VARIABLES, (array) $get('scoring_variables')));
                                    $settings->scoring_start_date = filled($get('scoring_start_date')) ? (string) $get('scoring_start_date') : null;
                                    $settings->save();

                                    $count = app(LeadScoring::class)->rebuildAndRescore();

                                    Notification::make()
                                        ->success()
                                        ->title(__('crm::filament/clusters/configurations/pages/manage-settings.form.scoring.updated', ['count' => $count]))
                                        ->send();
                                }),
                        ])->columnSpanFull(),
                    ]),
            ]);
    }

    protected bool $scoringChanged = false;

    protected function afterSave(): void
    {
        if (! $this->scoringChanged) {
            return;
        }

        $count = app(LeadScoring::class)->rebuildAndRescore();

        Notification::make()
            ->success()
            ->title(__('crm::filament/clusters/configurations/pages/manage-settings.form.scoring.updated', ['count' => $count]))
            ->send();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $current = app(CrmSettings::class);

        $data['scoring_variables'] = array_values(array_intersect(LeadScoring::VARIABLES, (array) ($data['scoring_variables'] ?? [])));
        $data['scoring_start_date'] = filled($data['scoring_start_date'] ?? null) ? (string) $data['scoring_start_date'] : null;

        $this->scoringChanged = $data['scoring_variables'] !== array_values((array) $current->scoring_variables)
            || $data['scoring_start_date'] !== $current->scoring_start_date;

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
