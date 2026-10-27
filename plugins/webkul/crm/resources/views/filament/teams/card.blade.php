@php
    use Webkul\Crm\Enums\LeadColor;
    use Webkul\Crm\Enums\TeamOrderScope;
    use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource as ConfigurationTeamResource;
    use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
    use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
    use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
    use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
    use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
    use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
    use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;

    $record = $getRecord();
    $dashboard = $getLivewire()->getTeamDashboard($record);
    $currency = $record->company?->currency?->code;
    $highest = max(1, ...array_map(fn (array $week): int => $week['count'], $dashboard['weeks']));
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
    $figures = array_filter([
        [
            'label'  => trans_choice('crm::filament/clusters/sales/resources/team.grid.open-opportunities', (int) $record->open_opportunities_count, ['count' => (int) $record->open_opportunities_count]),
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
            'amount' => null,
        ] : null,
    ]);
@endphp

<div @class(['crm-team-card', 'crm-team-card-colored' => $teamColor]) @if ($teamColor) style="--crm-team-color: {{ $teamColor }};" @endif>
    <div class="crm-team-card-header">
        <h3 class="truncate text-base font-semibold text-gray-950 dark:text-white">{{ $record->name }}</h3>

        <x-filament::dropdown placement="bottom-end" width="sm" teleport>
            <x-slot name="trigger">
                <x-filament::icon-button
                    icon="heroicon-m-ellipsis-vertical"
                    color="gray"
                    size="sm"
                    :label="__('crm::filament/clusters/sales/resources/team.grid.menu')"
                />
            </x-slot>

            <div class="crm-team-menu">
                <div class="crm-team-menu-sections">
                    @foreach ($menu as $section => $links)
                        <div class="crm-team-menu-section">
                            <p class="crm-team-menu-heading">{{ $menuLabels[$section] }}</p>

                            @foreach ($links as $link => $url)
                                <a href="{{ $url }}" class="crm-team-menu-link">{{ $menuLabels[$link] }}</a>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                @if ($editUrl)
                    <div class="crm-team-menu-footer">
                        <div class="crm-team-menu-colors">
                            <button
                                type="button"
                                wire:click="setTeamColor({{ $record->getKey() }}, 0)"
                                x-on:click="close()"
                                title="{{ __('crm::filament/clusters/sales/resources/team.grid-menu.no-color') }}"
                                class="crm-team-menu-color border border-gray-300 bg-white dark:border-white/20 dark:bg-gray-800"
                            >
                                @if (! $teamColor)
                                    <x-filament::icon icon="heroicon-m-check" class="h-3.5 w-3.5 text-gray-600" />
                                @endif
                            </button>

                            @foreach (LeadColor::cases() as $color)
                                <button
                                    type="button"
                                    wire:click="setTeamColor({{ $record->getKey() }}, {{ $color->value }})"
                                    x-on:click="close()"
                                    title="{{ $color->getLabel() }}"
                                    class="crm-team-menu-color"
                                    style="background-color: {{ $color->getHex() }};"
                                >
                                    @if (strcasecmp((string) $teamColor, $color->getHex()) === 0)
                                        <x-filament::icon icon="heroicon-m-check" class="h-3.5 w-3.5 text-white" />
                                    @endif
                                </button>
                            @endforeach
                        </div>

                        <a href="{{ $editUrl }}" class="crm-team-menu-link">
                            {{ __('crm::filament/clusters/sales/resources/team.grid.configuration') }}
                        </a>
                    </div>
                @endif
            </div>
        </x-filament::dropdown>
    </div>

    <div class="crm-team-card-body">
        <div class="crm-team-card-figures">
            @foreach ($figures as $figure)
                <a href="{{ $figure['url'] }}" class="truncate text-primary-600 hover:underline dark:text-primary-400">{{ $figure['label'] }}</a>
                <span class="text-end text-gray-950 dark:text-white">{{ $figure['amount'] }}</span>
            @endforeach
        </div>
    </div>

    <div class="crm-team-card-chart" role="img" aria-label="{{ __('crm::filament/clusters/sales/resources/team.grid.chart') }}">
        <div class="crm-team-card-bars">
            @foreach ($dashboard['weeks'] as $week)
                <div class="crm-team-card-bar-slot" title="{{ $week['label'] }}: {{ trans_choice('crm::filament/clusters/sales/resources/team.grid.new-opportunities', $week['count'], ['count' => $week['count']]) }}">
                    <div class="crm-team-card-bar" style="height: {{ round($week['count'] / $highest * 100) }}%"></div>
                </div>
            @endforeach
        </div>

        <div class="crm-team-card-labels">
            @foreach ($dashboard['weeks'] as $week)
                <span>{{ $week['label'] }}</span>
            @endforeach
        </div>
    </div>

    @if ($target > 0)
        <div class="crm-team-card-footer">
            <p>{{ __('crm::filament/clusters/sales/resources/team.grid.target-progress', ['invoiced' => money($dashboard['invoiced_month'], $currency), 'target' => money($target, $currency)]) }}</p>

            <div class="crm-team-card-progress">
                <div style="width: {{ min(100, round($dashboard['invoiced_month'] / $target * 100)) }}%"></div>
            </div>
        </div>
    @endif
</div>
