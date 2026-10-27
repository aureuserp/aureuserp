@use('Filament\Support\Facades\FilamentAsset')
@use('Webkul\Crm\Enums\ReportChartType')

<div class="crm-leads-graph crm-report-graph">
    <div class="crm-report-graph-toolbar">
        <label class="crm-report-graph-control">
            <span>{{ __('crm::filament/clusters/reporting/report.filters.measure') }}</span>

            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="graphMeasure">
                    @foreach ($measures as $key => $label)
                        <option value="{{ $key }}" @selected($filters['measure'] === $key)>{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </label>

        @if ($filters['chart_type'] !== ReportChartType::PIE->value)
            <label class="crm-report-graph-control">
                <span>{{ __('crm::filament/clusters/reporting/report.filters.split-by') }}</span>

                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="graphSplitBy">
                        <option value="" @selected(blank($filters['split_by']))>{{ __('crm::filament/clusters/reporting/report.filters.no-split') }}</option>

                        @foreach ($dimensions as $key => $label)
                            <option value="{{ $key }}" @selected($filters['split_by'] === $key)>{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>
        @endif

        <div class="crm-report-chart-tabs" role="group" aria-label="{{ __('crm::filament/clusters/reporting/report.filters.chart-type') }}">
            @foreach (ReportChartType::cases() as $type)
                <button
                    type="button"
                    class="fi-btn"
                    aria-pressed="{{ $filters['chart_type'] === $type->value ? 'true' : 'false' }}"
                    wire:click="$set('graphChartType', '{{ $type->value }}')"
                >
                    <x-filament::icon :icon="$type->getIcon()" class="fi-icon fi-size-md" />
                    {{ $type->getLabel() }}
                </button>
            @endforeach
        </div>
    </div>

    <h3 class="crm-report-graph-heading">{{ $chart['heading'] }}</h3>

    <div wire:key="crm-report-graph-{{ md5(json_encode([$chart['type'], $chart['data']])) }}">
        <div
            x-load
            x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
            wire:ignore
            x-data="chart({
                cachedData: @js($chart['data']),
                options: @js($chart['options']),
                type: @js($chart['type']),
            })"
            class="fi-wi-chart-frame fi-wi-chart-canvas-ctn fi-wi-chart-frame-no-aspect-ratio"
        >
            <canvas x-ref="canvas" aria-hidden="true" style="width: 100%; max-height: 26rem;"></canvas>

            <span aria-hidden="true" x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
            <span aria-hidden="true" x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
            <span aria-hidden="true" x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
            <span aria-hidden="true" x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
        </div>
    </div>
</div>
