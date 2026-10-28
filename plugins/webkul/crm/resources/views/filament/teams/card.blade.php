@php
    use Webkul\Crm\Enums\TeamOrderScope;
    use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource as ConfigurationTeamResource;
    use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
    use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
    use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
    use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
    use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
    use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
    use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
    use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
    use Webkul\Crm\Filament\Widgets\RecordsChart;

    $record = $getRecord();
    $dashboard = $getLivewire()->getTeamDashboard($record);
    $currency = $record->company?->currency?->code;
    $target = (float) $record->invoiced_target;
    $editUrl = ConfigurationTeamResource::canEdit($record) ? ConfigurationTeamResource::getUrl('edit', ['record' => $record]) : null;
    $teamColor = filled($record->color) ? $record->color : null;
    $reportFilters = ['filters' => ['team_ids' => ['values' => [$record->getKey()]]]];
    $menuLabels = [
        'view'          => __('crm::filament/clusters/sales/resources/team.grid-menu.view'),
        'reporting'     => __('crm::filament/clusters/sales/resources/team.grid-menu.reporting'),
        'leads'         => __('crm::filament/clusters/sales/resources/team.grid-menu.leads'),
        'opportunities' => __('crm::filament/clusters/sales/resources/team.grid-menu.opportunities'),
        'quotations'    => __('crm::filament/clusters/sales/resources/team.grid-menu.quotations'),
        'sales-orders'  => __('crm::filament/clusters/sales/resources/team.grid-menu.sales-orders'),
        'forecast'      => __('crm::filament/clusters/sales/resources/team.grid-menu.forecast'),
        'activities'    => __('crm::filament/clusters/sales/resources/team.grid-menu.activities'),
    ];
    $menu = array_filter([
        'view' => array_filter([
            'leads'         => LeadResource::canViewAny() ? LeadResource::getUrl('index', ['team' => $record->getKey()]) : null,
            'opportunities' => TeamResource::getUrl('opportunities', ['record' => $record, 'view' => 'list']),
            'quotations'    => QuotationResource::canViewAny() ? QuotationResource::getUrl('index', ['activeTableView' => 'default', 'team' => $record->getKey(), 'scope' => TeamOrderScope::QUOTATIONS->value]) : null,
            'sales-orders'  => QuotationResource::canViewAny() ? QuotationResource::getUrl('index', ['activeTableView' => 'default', 'team' => $record->getKey(), 'scope' => TeamOrderScope::SALES_ORDERS->value]) : null,
        ]),
        'reporting' => array_filter([
            'leads'         => LeadAnalysisResource::canViewAny() ? LeadAnalysisResource::getUrl('index', $reportFilters) : null,
            'opportunities' => PipelineAnalysisResource::canViewAny() ? PipelineAnalysisResource::getUrl('index', $reportFilters) : null,
            'forecast'      => ForecastResource::canViewAny() ? ForecastResource::getUrl('graph', $reportFilters) : null,
            'activities'    => ActivityAnalysisResource::canViewAny() ? ActivityAnalysisResource::getUrl('index', $reportFilters) : null,
        ]),
    ]);
    $openCount = (int) $record->open_opportunities_count;
    $stats = array_filter([
        [
            'label'  => trans_choice('crm::filament/clusters/sales/resources/team.grid.open-opportunities', $openCount, ['count' => $openCount]),
            'url'    => TeamResource::getUrl('opportunities', ['record' => $record, 'view' => 'list', 'activeTableView' => 'open']),
            'amount' => money((float) $record->open_expected_revenue, $currency),
        ],
        $dashboard['quotations'] > 0 ? [
            'label'  => trans_choice('crm::filament/clusters/sales/resources/team.grid.quotations', $dashboard['quotations'], ['count' => $dashboard['quotations']]),
            'url'    => QuotationResource::getUrl('index', ['activeTableView' => 'default', 'team' => $record->getKey(), 'scope' => TeamOrderScope::QUOTATIONS->value]),
            'amount' => money($dashboard['quotations_amount'], $currency),
        ] : null,
        $dashboard['orders_to_invoice'] > 0 ? [
            'label'  => trans_choice('crm::filament/clusters/sales/resources/team.grid.orders-to-invoice', $dashboard['orders_to_invoice'], ['count' => $dashboard['orders_to_invoice']]),
            'url'    => QuotationResource::getUrl('index', ['activeTableView' => 'default', 'team' => $record->getKey(), 'scope' => TeamOrderScope::TO_INVOICE->value]),
            'amount' => money($dashboard['orders_to_invoice_amount'], $currency),
        ] : null,
        $target > 0 ? [
            'label'  => __('crm::filament/clusters/sales/resources/team.grid.invoiced-month'),
            'url'    => null,
            'amount' => __('crm::filament/clusters/sales/resources/team.grid.of-target', ['invoiced' => money($dashboard['invoiced_month'], $currency), 'target' => money($target, $currency)]),
        ] : null,
    ]);
    $weekCounts = array_column($dashboard['weeks'], 'count');
    $isChartEmpty = array_sum($weekCounts) === 0;
    $chartData = [
        'labels'   => array_column($dashboard['weeks'], 'label'),
        'datasets' => [[
            'label'           => $isChartEmpty ? __('crm::filament/clusters/sales/resources/team.grid.sample-data') : __('crm::filament/clusters/sales/resources/team.grid.chart'),
            'data'            => $weekCounts,
            'backgroundColor' => $isChartEmpty ? '#ebebeb' : '#ef4444',
        ]],
    ];
    $chartOptions = [
        'plugins' => ['legend' => ['display' => false]],
        'scales'  => [
            'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
        ],
    ];
@endphp

<div class="crm-team-card">
    <x-filament::section>
        <x-slot name="heading">
            <span class="crm-team-card-heading">
                @if ($teamColor)
                    <span class="crm-kanban-color-dot" style="background-color: {{ $teamColor }};" title="{{ __('crm::filament/clusters/sales/resources/team.grid-menu.color') }}"></span>
                @endif

                <x-filament::link :href="TeamResource::getUrl('opportunities', ['record' => $record])">
                    {{ $record->name }}
                </x-filament::link>
            </span>
        </x-slot>

        <x-slot name="afterHeader">
            <div class="flex items-center gap-1">
                @if (OpportunityResource::canCreate())
                    <x-filament::button tag="a" :href="TeamResource::getUrl('opportunities', ['record' => $record, 'action' => 'create'])">
                        {{ __('crm::filament/clusters/sales/resources/team.grid.new') }}
                    </x-filament::button>
                @endif

                <x-filament::dropdown placement="bottom-end" width="sm" teleport>
                    <x-slot name="trigger">
                        <x-filament::icon-button
                            icon="heroicon-m-ellipsis-vertical"
                            color="gray"
                            :label="__('crm::filament/clusters/sales/resources/team.grid.menu')"
                        />
                    </x-slot>

                    <div class="crm-team-menu">
                        <div class="crm-team-menu-sections">
                            @foreach ($menu as $section => $sectionLinks)
                                <div class="crm-team-menu-section">
                                    <p class="crm-team-menu-heading">{{ $menuLabels[$section] }}</p>

                                    @foreach ($sectionLinks as $link => $url)
                                        <a href="{{ $url }}" class="crm-team-menu-link">{{ $menuLabels[$link] }}</a>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        @if ($editUrl)
                            <div class="crm-team-menu-footer">
                                <button type="button" class="crm-team-menu-link" x-on:click="close()" wire:click="mountAction('teamColor', { team: {{ $record->getKey() }} })">
                                    {{ __('crm::filament/clusters/sales/resources/team.grid-menu.color') }}
                                </button>

                                <a href="{{ $editUrl }}" class="crm-team-menu-link">
                                    {{ __('crm::filament/clusters/sales/resources/team.grid.configuration') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </x-filament::dropdown>
            </div>
        </x-slot>

        <div class="mb-4 flex flex-col items-end gap-3">
            @foreach ($stats as $stat)
                <div class="flex items-center gap-6">
                    @if ($stat['url'])
                        <x-filament::link :href="$stat['url']">{{ $stat['label'] }}</x-filament::link>
                    @else
                        <span class="text-sm font-semibold text-gray-950 dark:text-white">{{ $stat['label'] }}</span>
                    @endif

                    <span class="text-sm text-gray-950 dark:text-white">{{ $stat['amount'] }}</span>
                </div>
            @endforeach
        </div>

        <div class="crm-team-card-chart" role="img" aria-label="{{ __('crm::filament/clusters/sales/resources/team.grid.chart') }}">
            {{ RecordsChart::embed($chartData, $chartOptions) }}
        </div>
    </x-filament::section>
</div>
