@isset($target)
    <div wire:loading.block wire:target="{{ $target }}" class="crm-board-shimmer" aria-busy="true">
@endisset

<div class="crm-board-shimmer-columns">
    @foreach ([3, 2, 4, 2] as $cards)
        <div class="crm-board-shimmer-column">
            <div class="crm-board-shimmer-line crm-board-shimmer-title"></div>
            <div class="crm-board-shimmer-line crm-board-shimmer-bar"></div>

            @for ($card = 0; $card < $cards; $card++)
                <div class="crm-board-shimmer-card">
                    <div class="crm-board-shimmer-line" style="width: 70%;"></div>
                    <div class="crm-board-shimmer-line" style="width: 45%;"></div>
                    <div class="crm-board-shimmer-line" style="width: 30%;"></div>
                </div>
            @endfor
        </div>
    @endforeach
</div>

@isset($target)
    </div>
@endisset
