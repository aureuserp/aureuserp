@use('Webkul\Crm\Filament\Widgets\RecordsChart')

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
    </div>

    {{ RecordsChart::embed($chart['data'], $chart['options'], $chart['heading'], '26rem') }}
</div>
