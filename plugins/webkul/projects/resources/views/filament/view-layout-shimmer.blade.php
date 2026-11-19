@php
    use Webkul\Project\Enums\TaskViewLayout;
@endphp

@foreach ($layouts as $layout)
    @continue($layout === $activeLayout)

    <div
        wire:loading.block
        wire:target="switchTaskViewLayout('{{ $layout->value }}')"
        class="projects-view-shimmer"
        aria-busy="true"
    >
        @switch($layout)
            @case(TaskViewLayout::KANBAN)
                <div class="projects-board-shimmer-columns">
                    @foreach ([3, 2, 4, 2] as $cards)
                        <div class="projects-board-shimmer-column">
                            <div class="projects-board-shimmer-line projects-board-shimmer-title"></div>
                            <div class="projects-board-shimmer-line projects-board-shimmer-bar"></div>

                            @for ($card = 0; $card < $cards; $card++)
                                <div class="projects-board-shimmer-card">
                                    <div class="projects-board-shimmer-line" style="width: 70%;"></div>
                                    <div class="projects-board-shimmer-line" style="width: 45%;"></div>
                                    <div class="projects-board-shimmer-line" style="width: 30%;"></div>
                                </div>
                            @endfor
                        </div>
                    @endforeach
                </div>

                @break

            @case(TaskViewLayout::GRAPH)
                <div class="projects-graph-shimmer">
                    <div class="projects-board-shimmer-line projects-board-shimmer-title"></div>

                    <div class="projects-graph-shimmer-bars">
                        @foreach ([45, 70, 55, 90, 35, 65, 80, 50] as $height)
                            <div class="projects-board-shimmer-line projects-graph-shimmer-bar" style="height: {{ $height }}%;"></div>
                        @endforeach
                    </div>
                </div>

                @break

            @default
                <div class="projects-list-shimmer">
                    @for ($row = 0; $row < 8; $row++)
                        <div class="projects-list-shimmer-row">
                            <div class="projects-board-shimmer-line"></div>
                            <div class="projects-board-shimmer-line" style="width: {{ [80, 65, 90, 70][$row % 4] }}%;"></div>
                            <div class="projects-board-shimmer-line" style="width: {{ [60, 75, 50, 85][$row % 4] }}%;"></div>
                            <div class="projects-board-shimmer-line" style="width: {{ [70, 55, 80, 60][$row % 4] }}%;"></div>
                            <div class="projects-board-shimmer-line" style="width: 50%;"></div>
                        </div>
                    @endfor
                </div>
        @endswitch
    </div>
@endforeach
