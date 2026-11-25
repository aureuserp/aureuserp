@php
    $graphStateKey = $this->getKanbanGrouping().'-'.$this->graphType.'-'.($this->graphStacked ? 1 : 0).'-'.($this->graphSort ?? 'none');
@endphp

<div class="projects-graph">
    <div class="projects-graph-canvas" wire:key="projects-graph-canvas-{{ $graphStateKey }}">
        {{ $this->getTaskGraphEmbed() }}
    </div>
</div>
