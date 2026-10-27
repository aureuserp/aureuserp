@php
    $explanation ??= ['probability' => null, 'team' => null, 'positives' => [], 'negatives' => []];
    $isAutomated ??= true;
    $probability = $explanation['probability'];
    $percent = $probability === null ? 0 : max(0, min(100, (float) $probability));
    $circumference = 2 * M_PI * 15.5;
@endphp

<span
    x-data="{ open: false, alignRight: false, show() { this.alignRight = this.$el.getBoundingClientRect().left > window.innerWidth / 2; this.open = true } }"
    class="crm-pls"
    x-on:mouseenter="show()"
    x-on:mouseleave="open = false"
    x-on:click.stop="open ? open = false : show()"
>
    <span class="crm-pls-badge" title="{{ __('crm::filament/lead-scoring.popover.badge') }}">AI</span>

    <span x-cloak x-show="open" x-transition.opacity class="crm-pls-panel" x-bind:style="alignRight ? 'right: 0' : 'left: 0'">
        <span class="crm-pls-head">
            <svg width="44" height="44" viewBox="0 0 36 36" class="crm-pls-ring">
                <circle cx="18" cy="18" r="15.5" fill="none" stroke-width="3" class="crm-pls-ring-track" />
                <circle cx="18" cy="18" r="15.5" fill="none" stroke-width="3" class="crm-pls-ring-value"
                    stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $circumference * (1 - $percent / 100) }}"
                    transform="rotate(-90 18 18)" stroke-linecap="round" />
                <text x="18" y="20.5" text-anchor="middle" class="crm-pls-ring-text">{{ $probability === null ? '—' : number_format($percent, 2).'%' }}</text>
            </svg>

            <span>
                <span class="crm-pls-muted">{{ __('crm::filament/lead-scoring.popover.chances') }}</span>
                <span class="crm-pls-team">{{ $explanation['team'] ?? __('crm::filament/lead-scoring.popover.no-team') }}</span>
            </span>
        </span>

        @if ($probability === null)
            <span class="crm-pls-row crm-pls-muted">{{ __('crm::filament/lead-scoring.popover.no-history') }}</span>
        @else
            @if (! $isAutomated)
                <span class="crm-pls-row crm-pls-muted">{{ __('crm::filament/lead-scoring.popover.manual') }}</span>
            @endif

            @foreach (['positives' => ['crm-pls-up', __('crm::filament/lead-scoring.popover.positives')], 'negatives' => ['crm-pls-down', __('crm::filament/lead-scoring.popover.negatives')]] as $group => [$class, $groupTitle])
                @if (! empty($explanation[$group]))
                    <span class="crm-pls-title">{{ $groupTitle }}</span>

                    @foreach ($explanation[$group] as $factor)
                        <span class="crm-pls-row">
                            <x-filament::icon :icon="$group === 'positives' ? 'heroicon-m-arrow-up-right' : 'heroicon-m-arrow-down-right'" class="{{ $class }}" style="width: 1rem; height: 1rem; flex-shrink: 0;" />
                            <span>{{ $factor }}</span>
                        </span>
                    @endforeach
                @endif
            @endforeach
        @endif
    </span>
</span>

@once
    <style>
        .crm-pls { position: relative; display: inline-flex; align-items: center; vertical-align: middle; }
        .crm-pls-badge { cursor: pointer; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.02em; padding: 0 0.3rem; border-radius: 0.3rem; color: #fff; background: linear-gradient(135deg, #7c3aed, #db2777); }
        .crm-pls-panel { position: absolute; z-index: 50; top: 1.6rem; width: 19rem; display: flex; flex-direction: column; border-radius: 0.75rem; overflow: hidden; background: #fff; color: #111827; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); border: 1px solid #e5e7eb; text-align: left; font-weight: 400; }
        .crm-pls-head { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; }
        .crm-pls-head > span { display: flex; flex-direction: column; }
        .crm-pls-team { font-size: 1.05rem; font-weight: 700; }
        .crm-pls-muted { color: #6b7280; font-size: 0.85rem; }
        .crm-pls-title { padding: 0.45rem 0.75rem; font-weight: 600; background: #ede9fe; }
        .crm-pls-row { display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.75rem; font-size: 0.85rem; }
        .crm-pls-up { color: #16a34a; }
        .crm-pls-down { color: #dc2626; }
        .crm-pls-ring-track { stroke: #e5e7eb; }
        .crm-pls-ring-value { stroke: #7c3aed; }
        .crm-pls-ring-text { font-size: 7px; font-weight: 700; fill: currentColor; }
        .dark .crm-pls-panel { background: #18181b; color: #f4f4f5; border-color: #3f3f46; }
        .dark .crm-pls-title { background: #2e1065; }
        .dark .crm-pls-ring-track { stroke: #3f3f46; }
        .dark .crm-pls-muted { color: #a1a1aa; }
    </style>
@endonce
