<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\ColorPicker;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Webkul\Crm\Enums\TeamOrderScope;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource as ConfigurationTeamResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages\ListTeams;
use Webkul\Crm\Filament\Exports\TeamExporter;
use Webkul\Crm\Filament\Support\LeadTableFilters;
use Webkul\Crm\Filament\Widgets\RecordsChart;
use Webkul\Sale\Models\Team;

class TeamsTable
{
    public static function configure(Table $table, bool $grid = false): Table
    {
        $table = $grid
            ? $table
                ->columns(static::gridColumns())
                ->contentGrid(['xl' => 2, '2xl' => 3])
                ->recordUrl(null)
                ->recordActions(static::gridActions())
            : $table
                ->columns(static::listColumns())
                ->recordUrl(fn (Team $record): string => TeamResource::getUrl('opportunities', ['record' => $record]))
                ->recordActions([
                    Action::make('pipeline')
                        ->label(__('crm::filament/clusters/sales/resources/team.table.actions.pipeline.label'))
                        ->icon('heroicon-o-funnel')
                        ->url(fn (Team $record): string => TeamResource::getUrl('opportunities', ['record' => $record])),
                ]);

        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'company.currency']))
            ->filtersFormColumns(2)
            ->filters([
                LeadTableFilters::make([
                    TextConstraint::make('name')->label(__('crm::filament/clusters/sales/resources/team.table.columns.name'))->icon('heroicon-o-user-group'),
                    LeadTableFilters::relationship('user', __('crm::filament/clusters/sales/resources/team.table.filters.team-leader'), 'heroicon-o-user'),
                    LeadTableFilters::relationship('members', __('crm::filament/clusters/sales/resources/team.table.columns.members'), 'heroicon-o-users'),
                    LeadTableFilters::relationship('company', __('crm::filament/clusters/sales/resources/team.table.columns.company'), 'heroicon-o-building-office-2'),
                    DateConstraint::make('created_at')->label(__('crm::filament/clusters/sales/resources/team.table.filters.created-at')),
                ]),
            ])
            ->defaultSort('sort')
            ->toolbarActions([
                RecordBulkActions::make(exporter: TeamExporter::class, softDeletes: true),
            ]);
    }

    public static function listColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('crm::filament/clusters/sales/resources/team.table.columns.name'))
                ->description(fn (Team $record): ?string => $record->user?->name)
                ->searchable()
                ->sortable()
                ->weight('medium'),
            ColorColumn::make('color')
                ->label(''),
            TextColumn::make('open_opportunities_count')
                ->label(__('crm::filament/clusters/sales/resources/team.table.columns.open-opportunities'))
                ->badge()
                ->color('primary')
                ->sortable(),
            TextColumn::make('open_expected_revenue')
                ->label(__('crm::filament/clusters/sales/resources/team.table.columns.expected-revenue'))
                ->money(fn (Team $record): ?string => $record->company?->currency?->code)
                ->default(0)
                ->sortable(),
            TextColumn::make('members.name')
                ->label(__('crm::filament/clusters/sales/resources/team.table.columns.members'))
                ->badge()
                ->limitList(3)
                ->toggleable(),
            TextColumn::make('company.name')
                ->label(__('crm::filament/clusters/sales/resources/team.table.columns.company'))
                ->placeholder('—')
                ->toggleable(),
        ];
    }

    public static function gridColumns(): array
    {
        return [
            Stack::make([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/sales/resources/team.table.columns.name'))
                    ->weight(FontWeight::Bold)
                    ->size(TextSize::Large)
                    ->color('primary')
                    ->url(fn (Team $record): string => TeamResource::getUrl('opportunities', ['record' => $record]))
                    ->searchable()
                    ->sortable(),
                Stack::make([
                    Split::make([
                        TextColumn::make('open_opportunities_count')
                            ->formatStateUsing(fn (int $state): string => trans_choice('crm::filament/clusters/sales/resources/team.grid.open-opportunities', $state, ['count' => $state]))
                            ->color('primary')
                            ->url(fn (Team $record): string => TeamResource::getUrl('opportunities', ['record' => $record, 'layout' => 'list', 'activeTableView' => 'open'])),
                        TextColumn::make('open_expected_revenue')
                            ->money(fn (Team $record): ?string => $record->company?->currency?->code)
                            ->default(0)
                            ->alignEnd(),
                    ]),
                    Split::make([
                        TextColumn::make('quotations_count')
                            ->state(fn (Team $record, ListTeams $livewire): int => $livewire->getTeamDashboard($record)['quotations'])
                            ->formatStateUsing(fn (int $state): string => trans_choice('crm::filament/clusters/sales/resources/team.grid.quotations', $state, ['count' => $state]))
                            ->color('primary')
                            ->url(fn (Team $record): string => QuotationResource::getUrl('index', ['activeTableView' => 'default', 'team' => $record->getKey(), 'scope' => TeamOrderScope::QUOTATIONS->value])),
                        TextColumn::make('quotations_amount')
                            ->state(fn (Team $record, ListTeams $livewire): float => $livewire->getTeamDashboard($record)['quotations_amount'])
                            ->money(fn (Team $record): ?string => $record->company?->currency?->code)
                            ->alignEnd(),
                    ])
                        ->visible(fn (Team $record, ListTeams $livewire): bool => $livewire->getTeamDashboard($record)['quotations'] > 0),
                    Split::make([
                        TextColumn::make('orders_to_invoice_count')
                            ->state(fn (Team $record, ListTeams $livewire): int => $livewire->getTeamDashboard($record)['orders_to_invoice'])
                            ->formatStateUsing(fn (int $state): string => trans_choice('crm::filament/clusters/sales/resources/team.grid.orders-to-invoice', $state, ['count' => $state]))
                            ->color('primary')
                            ->url(fn (Team $record): string => QuotationResource::getUrl('index', ['activeTableView' => 'default', 'team' => $record->getKey(), 'scope' => TeamOrderScope::TO_INVOICE->value])),
                        TextColumn::make('orders_to_invoice_amount')
                            ->state(fn (Team $record, ListTeams $livewire): float => $livewire->getTeamDashboard($record)['orders_to_invoice_amount'])
                            ->money(fn (Team $record): ?string => $record->company?->currency?->code)
                            ->alignEnd(),
                    ])
                        ->visible(fn (Team $record, ListTeams $livewire): bool => $livewire->getTeamDashboard($record)['orders_to_invoice'] > 0),
                    Split::make([
                        TextColumn::make('invoiced_month_label')
                            ->state(fn (): string => __('crm::filament/clusters/sales/resources/team.grid.invoiced-month'))
                            ->weight(FontWeight::SemiBold),
                        TextColumn::make('invoiced_month')
                            ->state(fn (Team $record, ListTeams $livewire): string => __('crm::filament/clusters/sales/resources/team.grid.of-target', [
                                'invoiced' => money($livewire->getTeamDashboard($record)['invoiced_month'], $record->company?->currency?->code),
                                'target'   => money((float) $record->invoiced_target, $record->company?->currency?->code),
                            ]))
                            ->alignEnd(),
                    ])
                        ->visible(fn (Team $record): bool => (float) $record->invoiced_target > 0),
                ])->space(2),
                TextColumn::make('weekly_opportunities')
                    ->state(fn (Team $record, ListTeams $livewire): Htmlable => static::weeklyChart($livewire->getTeamDashboard($record)['weeks']))
                    ->extraAttributes(['role' => 'img', 'aria-label' => __('crm::filament/clusters/sales/resources/team.grid.chart')]),
            ])->space(4),
        ];
    }

    public static function gridActions(): array
    {
        return [
            static::colorAction('teamColorDot')
                ->hiddenLabel()
                ->iconButton()
                ->icon(fn (Team $record): Htmlable => new HtmlString('<span class="crm-team-color-dot" style="background-color: '.e($record->color).'"></span>'))
                ->tooltip(__('crm::filament/clusters/sales/resources/team.grid-menu.color'))
                ->visible(fn (Team $record): bool => filled($record->color))
                ->disabled(fn (Team $record): bool => ! ConfigurationTeamResource::canEdit($record)),
            Action::make('newOpportunity')
                ->label(__('crm::filament/clusters/sales/resources/team.grid.new'))
                ->button()
                ->url(fn (Team $record): string => TeamResource::getUrl('opportunities', ['record' => $record, 'action' => 'create']))
                ->visible(fn (): bool => OpportunityResource::canCreate()),
            ActionGroup::make([
                ActionGroup::make([
                    Action::make('viewLeads')
                        ->label(__('crm::filament/clusters/sales/resources/team.grid-menu.leads'))
                        ->icon('heroicon-o-funnel')
                        ->url(fn (Team $record): string => LeadResource::getUrl('index', ['team' => $record->getKey()]))
                        ->visible(fn (): bool => LeadResource::canViewAny()),
                    Action::make('viewOpportunities')
                        ->label(__('crm::filament/clusters/sales/resources/team.grid-menu.opportunities'))
                        ->icon('heroicon-o-star')
                        ->url(fn (Team $record): string => TeamResource::getUrl('opportunities', ['record' => $record, 'layout' => 'list'])),
                    Action::make('viewQuotations')
                        ->label(__('crm::filament/clusters/sales/resources/team.grid-menu.quotations'))
                        ->icon('heroicon-o-document-text')
                        ->url(fn (Team $record): string => QuotationResource::getUrl('index', ['activeTableView' => 'default', 'team' => $record->getKey(), 'scope' => TeamOrderScope::QUOTATIONS->value]))
                        ->visible(fn (): bool => QuotationResource::canViewAny()),
                    Action::make('viewSalesOrders')
                        ->label(__('crm::filament/clusters/sales/resources/team.grid-menu.sales-orders'))
                        ->icon('heroicon-o-shopping-bag')
                        ->url(fn (Team $record): string => QuotationResource::getUrl('index', ['activeTableView' => 'default', 'team' => $record->getKey(), 'scope' => TeamOrderScope::SALES_ORDERS->value]))
                        ->visible(fn (): bool => QuotationResource::canViewAny()),
                ])->dropdown(false),
                ActionGroup::make([
                    Action::make('leadAnalysis')
                        ->label(fn (): string => LeadAnalysisResource::getReportTitle())
                        ->icon('heroicon-o-chart-bar')
                        ->url(fn (Team $record): string => LeadAnalysisResource::getUrl('index', static::reportFilters($record)))
                        ->visible(fn (): bool => LeadAnalysisResource::canViewAny()),
                    Action::make('pipelineAnalysis')
                        ->label(fn (): string => PipelineAnalysisResource::getReportTitle())
                        ->icon('heroicon-o-chart-bar')
                        ->url(fn (Team $record): string => PipelineAnalysisResource::getUrl('index', static::reportFilters($record)))
                        ->visible(fn (): bool => PipelineAnalysisResource::canViewAny()),
                    Action::make('forecast')
                        ->label(fn (): string => ForecastResource::getReportTitle())
                        ->icon('heroicon-o-chart-bar')
                        ->url(fn (Team $record): string => ForecastResource::getUrl('index', [...static::reportFilters($record), 'layout' => 'graph']))
                        ->visible(fn (): bool => ForecastResource::canViewAny()),
                    Action::make('activityAnalysis')
                        ->label(fn (): string => ActivityAnalysisResource::getReportTitle())
                        ->icon('heroicon-o-chart-bar')
                        ->url(fn (Team $record): string => ActivityAnalysisResource::getUrl('index', static::reportFilters($record)))
                        ->visible(fn (): bool => ActivityAnalysisResource::canViewAny()),
                ])->dropdown(false),
                ActionGroup::make([
                    Action::make('configuration')
                        ->label(__('crm::filament/clusters/sales/resources/team.grid.configuration'))
                        ->icon('heroicon-o-cog-6-tooth')
                        ->url(fn (Team $record): string => ConfigurationTeamResource::getUrl('edit', ['record' => $record]))
                        ->visible(fn (Team $record): bool => ConfigurationTeamResource::canEdit($record)),
                    static::colorAction('teamColor')
                        ->label(__('crm::filament/clusters/sales/resources/team.grid-menu.color'))
                        ->icon('heroicon-o-swatch')
                        ->authorize(fn (Team $record): bool => ConfigurationTeamResource::canEdit($record)),
                ])->dropdown(false),
            ])
                ->label(__('crm::filament/clusters/sales/resources/team.grid.menu'))
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray'),
        ];
    }

    protected static function colorAction(string $name): Action
    {
        return Action::make($name)
            ->modalHeading(__('crm::filament/clusters/sales/resources/team.grid-menu.color'))
            ->modalWidth('sm')
            ->modalFooterActionsAlignment(Alignment::End)
            ->fillForm(fn (Team $record): array => ['color' => $record->color])
            ->schema([
                ColorPicker::make('color')
                    ->label(__('crm::filament/clusters/sales/resources/team.grid-menu.color'))
                    ->hexColor(),
            ])
            ->action(fn (array $data, Team $record): bool => $record->update(['color' => filled($data['color'] ?? null) ? $data['color'] : null]));
    }

    protected static function reportFilters(Team $team): array
    {
        return ['filters' => ['team_ids' => ['values' => [$team->getKey()]]]];
    }

    protected static function weeklyChart(array $weeks): Htmlable
    {
        $counts = array_column($weeks, 'count');
        $isEmpty = array_sum($counts) === 0;

        return RecordsChart::embed(
            [
                'labels'   => array_column($weeks, 'label'),
                'datasets' => [[
                    'label'           => $isEmpty ? __('crm::filament/clusters/sales/resources/team.grid.sample-data') : __('crm::filament/clusters/sales/resources/team.grid.chart'),
                    'data'            => $counts,
                    'backgroundColor' => $isEmpty ? '#ebebeb' : '#ef4444',
                ]],
            ],
            [
                'plugins' => ['legend' => ['display' => false]],
                'scales'  => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
            ],
            class: 'crm-team-card-chart',
        );
    }
}
