@php
    $chartTypes = [
        'bar'  => 'heroicon-m-chart-bar',
        'line' => 'heroicon-m-presentation-chart-line',
        'pie'  => 'heroicon-m-chart-pie',
    ];
@endphp

<div class="projects-graph-controls">
    <div class="projects-layout-switcher" role="group">
        @foreach ($chartTypes as $type => $icon)
            <x-filament::icon-button
                :icon="$icon"
                :color="$graphType === $type ? 'primary' : 'gray'"
                :label="__('projects::filament/kanban.graph.types.'.$type)"
                :tooltip="__('projects::filament/kanban.graph.types.'.$type)"
                :aria-selected="$graphType === $type ? 'true' : 'false'"
                wire:click="setGraphType('{{ $type }}')"
            />
        @endforeach
    </div>

    @if ($graphType === 'bar')
        <x-filament::icon-button
            icon="heroicon-m-square-3-stack-3d"
            :color="$graphStacked ? 'primary' : 'gray'"
            :label="__('projects::filament/kanban.graph.stacked')"
            :tooltip="__('projects::filament/kanban.graph.stacked')"
            :aria-selected="$graphStacked ? 'true' : 'false'"
            wire:click="toggleGraphStacked"
        />
    @endif

    <div class="projects-layout-switcher" role="group">
        <x-filament::icon-button
            icon="heroicon-m-bars-arrow-down"
            :color="$graphSort === 'desc' ? 'primary' : 'gray'"
            :label="__('projects::filament/kanban.graph.sort-desc')"
            :tooltip="__('projects::filament/kanban.graph.sort-desc')"
            :aria-selected="$graphSort === 'desc' ? 'true' : 'false'"
            wire:click="setGraphSort('desc')"
        />

        <x-filament::icon-button
            icon="heroicon-m-bars-arrow-up"
            :color="$graphSort === 'asc' ? 'primary' : 'gray'"
            :label="__('projects::filament/kanban.graph.sort-asc')"
            :tooltip="__('projects::filament/kanban.graph.sort-asc')"
            :aria-selected="$graphSort === 'asc' ? 'true' : 'false'"
            wire:click="setGraphSort('asc')"
        />
    </div>
</div>
