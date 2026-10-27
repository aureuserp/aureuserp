@use('Filament\Support\Facades\FilamentAsset')

<div class="crm-leads-graph" wire:key="crm-leads-graph-{{ md5(json_encode($chart['data'])) }}">
    <div
        x-load
        x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
        wire:ignore
        x-data="chart({
            cachedData: @js($chart['data']),
            options: @js($chart['options']),
            type: 'bar',
        })"
        class="fi-wi-chart-frame fi-wi-chart-canvas-ctn fi-wi-chart-frame-no-aspect-ratio"
    >
        <canvas x-ref="canvas" aria-hidden="true" style="width: 100%; max-height: 24rem;"></canvas>

        <span aria-hidden="true" x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
        <span aria-hidden="true" x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
        <span aria-hidden="true" x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
        <span aria-hidden="true" x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
    </div>
</div>
