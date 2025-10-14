@php
    use Filament\Infolists\Components\IconEntry\IconEntrySize;

    $stateValue = $getState();

    if ($stateValue instanceof Closure) {
        $stateValue = $stateValue();
    }

    $total = 100;
    $progress = ($stateValue / $total) * 100;
    $displayProgress = $progress == 100 ? number_format($progress, 0) : number_format($progress, 2);

    $color = $getColor($state) ?? 'gray';

    $colorMap = [
        'primary'   => 'rgba(13, 110, 253, 1)',  
        'secondary' => 'rgba(108, 117, 125, 1)', 
        'success'   => 'rgba(25, 135, 84, 1)',   
        'danger'    => 'rgba(220, 53, 69, 1)',   
        'warning'   => 'rgba(255, 193, 7, 1)',   
        'info'      => 'rgba(13, 202, 240, 1)',  
        'light'     => 'rgba(248, 249, 250, 1)', 
        'dark'      => 'rgba(33, 37, 41, 1)',    
    ];

    $bgColor = $colorMap[$color] ?? $color;

@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-in-progress-bar flex flex-wrap gap-1.5',
                ])
        }}
    >
        <div class="progress-container">
            <div class="progress-bar" style="width: {{ $displayProgress }}%; background-color: {{ $bgColor }};"></div>

            <div class="progress-text">
                <small
                    @class([
                        'text-gray-700' => $displayProgress != 100,
                        'text-white' => $displayProgress == 100
                    ])
                >
                    {{ $displayProgress }}%
                </small>
            </div>
        </div>
    </div>
</x-dynamic-component>

<style>
    .progress-container {
        width: 100%;
        background-color: #e5e7eb;
        border-radius: 0.375rem;
        height: 1.5rem;
        overflow: hidden;
        position: relative;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .progress-bar {
        height: 100%;
        border-radius: 0.375rem;
        transition: width 0.3s, background-color 0.3s;
    }
    .progress-text {
        text-align: center;
        font-size: 0.875rem;
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
    }
</style>
