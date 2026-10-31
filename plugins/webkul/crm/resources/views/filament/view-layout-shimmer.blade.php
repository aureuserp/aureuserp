@php
    use Webkul\Crm\Enums\ViewLayout;
@endphp

@foreach ($layouts as $layout)
    @continue($layout === $activeLayout)

    <div
        wire:loading.block
        wire:target="switchViewLayout('{{ $layout->value }}')"
        class="crm-view-shimmer"
        aria-busy="true"
    >
        @switch($layout)
            @case(ViewLayout::KANBAN)
                @include('crm::filament.kanban.shimmer')

                @break

            @case(ViewLayout::GRID)
                <div class="crm-grid-shimmer">
                    @for ($card = 0; $card < 6; $card++)
                        <div class="crm-board-shimmer-card">
                            <div class="crm-board-shimmer-line crm-board-shimmer-title"></div>
                            <div class="crm-board-shimmer-line" style="width: 70%;"></div>
                            <div class="crm-board-shimmer-line" style="width: 55%;"></div>
                            <div class="crm-board-shimmer-line crm-grid-shimmer-block"></div>
                        </div>
                    @endfor
                </div>

                @break

            @case(ViewLayout::GRAPH)
                <div class="crm-graph-shimmer">
                    <div class="crm-board-shimmer-line crm-board-shimmer-title"></div>

                    <div class="crm-graph-shimmer-bars">
                        @foreach ([45, 70, 55, 90, 35, 65, 80, 50] as $height)
                            <div class="crm-board-shimmer-line crm-graph-shimmer-bar" style="height: {{ $height }}%;"></div>
                        @endforeach
                    </div>
                </div>

                @break

            @default
                <div class="crm-list-shimmer">
                    @for ($row = 0; $row < 8; $row++)
                        <div class="crm-list-shimmer-row">
                            <div class="crm-board-shimmer-line"></div>
                            <div class="crm-board-shimmer-line" style="width: {{ [80, 65, 90, 70][$row % 4] }}%;"></div>
                            <div class="crm-board-shimmer-line" style="width: {{ [60, 75, 50, 85][$row % 4] }}%;"></div>
                            <div class="crm-board-shimmer-line" style="width: {{ [70, 55, 80, 60][$row % 4] }}%;"></div>
                            <div class="crm-board-shimmer-line" style="width: 50%;"></div>
                        </div>
                    @endfor
                </div>
        @endswitch
    </div>
@endforeach
